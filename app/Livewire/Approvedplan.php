<?php

namespace App\Livewire;

use App\Exports\ExportMultipleSheetPlan;
use App\Exports\PlandueExport;
use App\Exports\ShortPlanExport;
use App\Exports\TagsExport;
use App\Models\Listitem;
use App\Models\Opland;
use App\Models\Part;
use App\Models\Plandue;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem as BlockListItem;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Approvedplan extends Component
{

    use WithPagination;

    #[Url(history:true)]
    public $search = "";

    public $mydata = "";

    #[Url(history:true)]
    public $company = "";

    public $movemodal = false;
    public $planid;
    
    
    public function export($id) 
    {
        $exportplan = Plandue::where('id', $id)->with('listitems')->with('createBy')->first();
        if (!$exportplan) {
                    return response()->json(['error' => 'Plandue not found'], 404);
                }
        $test = $exportplan->listitems;
        $test2 = $exportplan->createBy;
        $sumvalue = Listitem::select('outpart', 'po','pr','customer', DB::raw('SUM(quantity) as total_quantity'),DB::raw('SUM(prize) as total_price'))
        ->where('plandue_id', $id)
        ->groupBy('outpart', 'po', 'customer','pr') // Include customer in the group by clause
        ->get();
        // dd($sumvalue);
        // dd(file_exists("img/J1A-F217G-00-00-80.jpg"));
        
        return Excel::download(new ExportMultipleSheetPlan($exportplan, $test, $test2,$sumvalue),'tag.xlsx');

    }

    public function markDone($id,$status)
    {
        
        try{
            if($status == 'approved'){
                Plandue::where('id',$id)->update(['status'=>'done']);
            }
        }
        catch(\Exception $e)
        {

        }
    }

   

    public function openMoveModal($id) {
        $this->planid = $id;
        $this->movemodal = true;
    }

    public function movePlan(){
        try{
            
            Plandue::where('id',$this->planid)->update(['status'=>'pending']);
            $this->closeMoveModal();
        }
        catch(\Exception $e)
        {

        }
    }



    public function closeMoveModal(){
        $this->movemodal = false;
        $this->reset(['planid']);
    }
    
    public function oracle($id){
        try{
        $oracle = Plandue::with('listitems')->find($id);
        $summedItems = $oracle->listitems()
        ->select('customer','outpart', 'po', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(prize) as total_price'))
        ->groupBy('outpart', 'po','customer')
        ->get();
        
        $part = Part::where('customer',$oracle->listitems->first()->customer)->where('outpart',$oracle->listitems->first()->outpart)->first();
        $checkExist = Opland::where('legacy_so_num','LIKE',$oracle->plan_id . '%')->first();
        }catch(\Exception $e){
            session()->flash('error', 'can not get plan id'); 
        }
        if (!$part->bill_to || !$part->order_type || !$part->price_list || !$part->sale_reps) {
            session()->flash('error', 'Not enough data for oracle');
        }
        else{
            if(empty($checkExist)){
            DB::beginTransaction();
            try{
            $uniquePo = $summedItems->pluck('po')->unique();
            
            foreach($uniquePo as $po){
    
                $part = Part::where('customer', $oracle->listitems->first()->customer)
                ->where('outpart', function ($query) use ($oracle, $po) {
                    $query->select('outpart')
                          ->from('listitems') // Make sure 'list_items' is the correct table name
                          ->where('po', $po)->first();
                })->first();
                if (!$part->bill_to || !$part->order_type || !$part->price_list || !$part->sale_reps) {
                    session()->flash('error', 'Not enough data for oracle');
                }
            $pland = Opland::create([
                'legacy_so_num' => ($oracle->plan_id)." ".($po),
                'customer_no'=>$oracle->listitems->first()->customer,
                'bill_to'=>$part->bill_to,
                'transaction_type'=>$part->order_type,
                'order_date'=>$oracle->duedate,
                'price_list'=>$part->price_list,
                'salesperson'=>$part->sale_reps,
                'customer_po_no' => $po,
                'warehouse'=>'TRU',
                
            ]);
            $line = 1;
            foreach ($summedItems->where('po',$po) as $item) {
                $filteredItem = $oracle->listitems->where('po', $po)->where('outpart', $item->outpart)->first();
                $pland->olist()->create([
                    'item_code' => Part::where('customer',$item->customer)->where('outpart',$item->outpart)->pluck('trupart')->first(),
                    'qty'=>$item->total_quantity,
                    'price_unit'=>$item->total_price / $item->total_quantity,
                    'customer_part_number'=> $item->outpart,
                    'po_number'=>$po,
                    'pr_number'=>$item->pr,
                    'issue_number'=>$filteredItem->issue,
                    'line_num'=> $line
                ]);
                $line += 1;     
                }
            }
            DB::commit();
            session()->flash('success', ' Commit data to oracle succesfuly'); 
            }catch(\Exception $e){
                DB::rollBack();
                session()->flash('error', 'can not insert data into oracle pls try again later.'); 
            }
        
        }
            else{
                session()->flash('error', '  Plan already exist in oracle. If you want to commit this in to oracle, Please delete data in oracle first.'); 
            }
        }
    }

    public function oneoracle($id){
        try{
        $oracle = Plandue::with('listitems')->find($id);
        $summedItems = $oracle->listitems()
        ->select('customer','outpart', 'po','pr', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(prize) as total_price'))
        ->groupBy('outpart', 'po','customer','pr')
        ->get();
        $part = Part::where('customer',$oracle->listitems->first()->customer)->where('outpart',$oracle->listitems->first()->outpart)->first();
        $checkExist = Opland::where('legacy_so_num',$oracle->plan_id)->first();
        } catch(\Exception $e){
            session()->flash('error', 'can not get plan id'); 
        }
        
        if (!$part->bill_to || !$part->order_type || !$part->price_list || !$part->sale_reps) {
            session()->flash('error', 'Not enough data for oracle');
        }
        else{
            if(empty($checkExist)){
                try{
                $part = Part::where('customer',$oracle->listitems->first()->customer)->where('outpart',$oracle->listitems->first()->outpart)->first();
                $pland = Opland::create([
                    'legacy_so_num' => $oracle->plan_id,
                    'customer_no'=>$oracle->listitems->first()->customer,
                    'bill_to'=>$part->bill_to,
                    'transaction_type'=>$part->order_type,
                    'order_date'=>$oracle->duedate,
                    'price_list'=>$part->price_list,
                    'salesperson'=>$part->sale_reps,
                    'warehouse'=>'TRU', 
                ]);
                 foreach ($summedItems as $index=>$item) {
                $filteredItem = $oracle->listitems->where('po', $item->po)->where('outpart', $item->outpart)->first();
                $pland->olist()->create([
                    'item_code' => Part::where('customer',$item->customer)->where('outpart',$item->outpart)->pluck('trupart')
                    ->first(),
                    'qty'=>$item->total_quantity,
                    'price_unit'=>$item->total_price / $item->total_quantity,
                    'customer_part_number'=> $item->outpart,
                    'po_number'=>$item->po,
                    'pr_number'=>$item->pr,
                    'issue_number'=>$filteredItem->issue,
                    'line_num'=> $index+1
                ]);
                DB::commit();
                session()->flash('success', ' Commit data to oracle succesfuly');
                }
                }catch(\Exception $e){
                    DB::rollBack();
                    session()->flash('error', 'can not insert data into oracle pls try again later.'); 
                }
            }
            else{
                session()->flash('error', '  Plan already exist in oracle. If you want to commit this in to oracle, Please delete data in oracle first.'); 
            }
        }
    }
    
public function render()
    {
        $get_com = Plandue::whereNotNull('company_name')->distinct()->pluck('company_name');
        $plands = Plandue::whereNot('status','pending')
        ->with('listitems')
        ->when($this->mydata !== "", function($query){
            $query->where('created_by',auth()->id());
        })
        ->when($this->company !== "" , function($query){
            $query->where('company_name',$this->company);
        })
            ->searchpland($this->search)
            ->orderBy('id','desc')
            ->paginate(10);

        return view('livewire.approvedplan',compact('plands','get_com'));
    }
}

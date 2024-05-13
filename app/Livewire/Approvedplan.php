<?php

namespace App\Livewire;

use App\Exports\ExportMultipleSheetPlan;
use App\Exports\PlandueExport;
use App\Exports\ShortPlanExport;
use App\Exports\TagsExport;
use App\Models\Listitem;
use App\Models\Plandue;
use Illuminate\Support\Facades\DB;
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
    
    
    public function export($id) 
    {
        $exportplan = Plandue::where('id', $id)->with('listitems')->with('createBy')->first();
        if (!$exportplan) {
                    return response()->json(['error' => 'Plandue not found'], 404);
                }
        $test = $exportplan->listitems;
        $test2 = $exportplan->createBy;
        $sumvalue = Listitem::select('outpart', 'customer', DB::raw('SUM(quantity) as total_quantity'),DB::raw('SUM(prize) as total_price'))
        ->where('plandue_id', $id)
        ->groupBy('outpart', 'customer') // Include customer in the group by clause
        ->get();
        
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

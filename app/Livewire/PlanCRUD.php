<?php

namespace App\Livewire;

use App\Imports\ListitemImport;
use App\Models\Listitem;
use App\Models\Part;
use App\Models\Plandue;
use App\Rules\ExceedsLimit;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


class PlanCRUD extends Component
{
    use WithFileUploads;
    public $showCreateModal = false;
    public $itemDetails = [
        ['duedate' => '', 'issue' => '', 'outpart' => '', 'quantity' => ''],
    ];
    public $getOutpartSuggestions;

    public $csvFile;
    public $duplicateInput = false;  
    
    protected $rules = [
        'itemDetails.*.issue' => 'uniqueIssue',
    ];
    
    public function processCsv()
    {
        $this->validate([
            'csvFile' => ['required','mimes:csv'],
        ]);

        $data = Excel::toArray(new ListitemImport, $this->csvFile);
       if (!empty($data) && count($data) > 0){
        $headers = $data[0][0];
        $this->itemDetails = array_fill_keys($headers, '');

        foreach ($data[0] as $row) {
        if($row[0] !== 'duedate'){     
            $duedate = $row[0];
            $issue = $row[1];
            $outpart = $row[2];
            $quantity = $row[3];
        
            
            $itemDetails = [
                'duedate' => $duedate,
                'issue' => $issue,
                'outpart' => $outpart,
                'quantity' => $quantity,
            ];

            $itemDetailsArray[] = $itemDetails;
        }
        }
        $this->itemDetails = $itemDetailsArray;
        // dd($this->itemDetails);
       }
    }

    public function rules()
    {
        return [
            'itemDetails.*.issue' => 'uniqueIssue',
        ];
    }

    protected function getValidationAttributes()
    {
        return [
            'itemDetails.*.issue' => 'Issue',
        ];
    }

    protected function validateUniqueIssue($attribute, $value, $parameters)
    {
        $existingIssues = collect($this->itemDetails)->pluck('issue');

        return !$existingIssues->contains($value);
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function hideCreateModal()
    {
        $this->duplicateInput = false;
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function addItem()
    {
        $this->itemDetails[] = ['duedate' => '', 'issue' => '', 'outpart' => '', 'quantity' => ''];
    }

    public function clearItem()
    {
        $this->reset('itemDetails');
        $this->resetValidation();
    }

    public function removeItem($index)
    {
        unset($this->itemDetails[$index]);
        
        $this->itemDetails = array_values($this->itemDetails);// Re-index the array
    }

    public function checkQuantity()
{
    $this->validate([
        'itemDetails.*.duedate' => 'required|date',
        'itemDetails.*.issue' => 'string',
        'itemDetails.*.outpart' => ['required','string',Rule::exists('parts','outpart')],
        'itemDetails.*.quantity' => 'required|numeric',
    ],
    [
        'itemDetails.*.duedate.required' => 'DueDate is required.',
        'itemDetails.*.duedate.date' => 'Date must be a valid date.',
        'itemDetails.*.outpart.required' => 'Outpart field is required.',
        'itemDetails.*.outpart.exists' => 'Outpart does not exist.',
        'itemDetails.*.quantity.required' => 'Quantity field is required.',
        'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
    ]);

    if (!empty($this->itemDetails)) {
        $currentDate = Carbon::now()->format('Ymd');
        $searchPattern = "IS-{$currentDate}-";

        foreach($this->itemDetails as $index => $checkIssue) {
            if (empty($checkIssue['issue'])) {
                $latestIssue = Listitem::latest()->where('issue', 'LIKE', "%{$searchPattern}%")->first();
                $countIssue = $latestIssue ? (int) substr($latestIssue->issue, -4) + 1 : 1;
                $issueCounterFormatted = sprintf('%04d', $countIssue);
                $customIssue = "IS-{$currentDate}-{$issueCounterFormatted}";
                $this->itemDetails[$index]['issue'] = $customIssue;
                $latestNumber = 0;
            }
            else
            {
                $latestIssue = Listitem::where('issue', 'LIKE' ,"%-{$checkIssue['issue']}")->orderByDesc('issue')->first();
                // dd($latestIssue);
                $latestNumber = $latestIssue ? (int) explode('-', $latestIssue->issue)[0] : 0;
                // dd(array( $latestNumber));
            }

            if (!empty($checkIssue['outpart'])) {
              
                $maxQuantity = Part::where('outpart',$checkIssue['outpart'])->first();
                $snp = $maxQuantity->snp ?? null;
                
                if ($snp && $checkIssue['quantity'] > $snp) {
                    $setNeeded = ceil($checkIssue['quantity'] / $snp);
                    for ($i = 1; $i <= $setNeeded; $i++) {
                        $count = $latestNumber + $i;
                        $quantityToAdd = min($checkIssue['quantity'], $snp);
                        $this->itemDetails[] = [
                            'duedate' => $this->itemDetails[$index]['duedate'],
                            'issue' => "{$count}-{$this->itemDetails[$index]['issue']}",
                            'outpart' => $this->itemDetails[$index]['outpart'],
                            'quantity' => $quantityToAdd,
                        ];
                        $checkIssue['quantity'] -= $quantityToAdd;
                    }
                    array_splice($this->itemDetails, $index, 1);
                }
                else
                {
                    
                }
            }
        }
    }
}

    public function createPlan()
    {
        $this->duplicateInput = false;
        $currentDate = Carbon::now()->format('Ymd');
        $latestRecord = Plandue::latest()->whereDate('created_at',$currentDate)->first();
        $counter = $latestRecord ? (int) substr($latestRecord->plan_id, -4) + 1 : 1;
        $counterFormatted = sprintf('%04d', $counter);
        $customId = "DP-{$currentDate}-{$counterFormatted}";
        
        if (count(array_column($this->itemDetails, 'issue')) !== count(array_unique(array_column($this->itemDetails, 'issue')))) {
            $this->duplicateInput = true;
        } 
        else {
        

            $this->validate([
                'itemDetails.*.duedate' => 'required|date',
                'itemDetails.*.issue' => 'required|string|unique:listitems,issue',
                'itemDetails.*.outpart' => ['required','string',Rule::exists('parts','outpart')],
                'itemDetails.*.quantity' =>
                ['required', 'numeric', 
                function ($attribute, $value, $fail) {
                    
                    $index = explode('.', $attribute)[1];
                    $outpart = $this->itemDetails[$index]['outpart'] ?? null;
                    // if (!$outpart) {
                    //     $fail("The outpart for index $index is missing.");
                    //     return;
                    // }
                    $limit = Part::where('outpart', $outpart)->value('snp');
                      if(is_null($limit)){
                        $fail("");
                      }
        
                    if ($value > $limit) {
                        $fail("The quantity for $outpart exceeds the limit.");
                    }
                },] ,
            ],[
                'itemDetails.*.duedate.required' => 'DueDate is required.',
                'itemDetails.*.duedate.date' => 'Date must be valid date.',
                'itemDetails.*.issue.required' => 'Issue field is required.',
                'itemDetails.*.issue.unique' => 'Issue must be unique.',
                'itemDetails.*.outpart.required' => 'Outpart field is required.',
                'itemDetails.*.outpart.exists' => 'Outpart does not exist.',
                'itemDetails.*.quantity.required' => 'Quantity field is required.',
                'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
                'itemDetails.*.quantity.exceeds_limit' => 'The quantity exceeds the maximum limit for the outpart.',
            ]);

            $plan = Plandue::create([
                'plan_id' => $customId,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($this->itemDetails as $item) {
                Listitem::create([
                    'plandue_id' => $plan->id,
                    'created_by' => auth()->id(),
                    'duedate' => $item['duedate'],
                    'issue' => $item['issue'],
                    'outpart' => $item['outpart'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $this->reset('itemDetails');
            $this->hideCreateModal();
        } 
    }

    public $deleteId;
    public $selectedPlan;
    public $showDeleteModal = false;

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;
        $this->selectedPlan = Plandue::find($id);
        $this->showDeleteModal = true;
    }

    public function hideDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset('deleteId');
        $this->reset('selectedPlan');
        
    }

    public function deleteData()
    {
        Plandue::where('id', $this -> selectedPlan['id'])->delete();
        $this->hideDeleteModal();
    }

    public $editItems; 
    public $showEditModal=false;
    public $editItemDetails=[];
    public $editId;
    public $countEditItems;

    public $deleteItem;

    public function openEditModal($id)
    {   
        $this->editId = $id;
        $this->editItems = Listitem::select('id','outpart','duedate','issue','quantity')->where('plandue_id',$id)->get();
        // dd($this->editItems->toArray());
        if(!empty($this->editItems))
        {
            foreach($this->editItems as $editItem)
            {
            $this->editItemDetails[] = ['id'=>$editItem->id , 'duedate' => $editItem->duedate , 'issue' => $editItem->issue, 'outpart' => $editItem->outpart, 'quantity' =>$editItem->quantity];
            }
        }
        $this->countEditItems = count($this->editItemDetails);


        $this->showEditModal = true;
    }
    
    public function editPlan()
        {
            $this->duplicateInput = false;
            if (count(array_column($this->editItemDetails, 'issue')) !== count(array_unique(array_column($this->editItemDetails, 'issue')))) {
            $this->duplicateInput = true;
            } 

                foreach($this->editItemDetails as $index  => $item)           
                $this->validate([
                    "editItemDetails.$index.duedate" => "required|date",
                    "editItemDetails.$index.issue" => "required|string|unique:listitems,issue," . $item['id'],
                    "editItemDetails.$index.outpart" => ['required','string',Rule::exists('parts','outpart')],
                    "editItemDetails.$index.quantity" => "required|numeric",
                    ],[
                    "editItemDetails.$index.duedate.required" => "DueDate is required.",
                    "editItemDetails.$index.duedate.date" => 'Date must be valid date.',
                    "editItemDetails.$index.issue.required" => 'Issue field is required.',
                    "editItemDetails.$index.issue.unique" => 'Issue must be unique.',
                    "editItemDetails.$index.outpart.required" => 'Outpart field is required.',
                    "editItemDetails.$index.outpart.exists" => 'Outpart does not exist.',
                    "editItemDetails.$index.quantity.required" => 'Quantity field is required.',
                    "editItemDetails.$index.quantity.numeric" => 'Quantity must be numeric.',
                    ]);  
                 
                // dd($test);                                
            // }
            
            if(!empty($this->deleteItem))
            {   
                foreach($this->deleteItem as $deleteI)
                    {
                        Listitem::where('id',$deleteI['id'])->delete();
                    }
            }

            foreach($this->editItemDetails as $updateItem)
                {
                    if(empty($updateItem['id']))
                    {
                       break;
                    } 
                    Listitem::where('id', $updateItem['id'])->update([
                            'duedate' => $updateItem['duedate'],
                            'issue' => $updateItem['issue'],
                            'outpart'=>$updateItem['outpart'],
                            'quantity'=>$updateItem['quantity'],
                            'updated_by' => auth()->id(),
                        ]);
                }
            foreach(array_reverse($this->editItemDetails) as $createInUpdate)
            {
                if(!empty($createInUpdate['id']))
                    {
                        break;
                    }
                Listitem::create([
                    'plandue_id' => $this->editId,
                    'created_by' => auth()->id(),
                    'duedate' => $createInUpdate['duedate'],
                    'issue' => $createInUpdate['issue'],
                    'outpart' => $createInUpdate['outpart'],
                    'quantity' => $createInUpdate['quantity'],
                ]);
            }
        }

    public function editRemove($index)
    {

        $deleteId = $this->editItemDetails[$index]['id']; 
        if(!empty($deleteId)){
        $this->deleteItem[] = ['id'=>$deleteId];
        }
        unset($this->editItemDetails[$index]);
        $this->editItemDetails = array_values($this->editItemDetails);
        $this->countEditItems = count($this->editItemDetails);
    }
    public function editAdd()
    {
        
        $this->editItemDetails[] = ['id'=> '','duedate' => '', 'issue' => '', 'outpart' => '', 'quantity' => ''];
        $this->countEditItems = count($this->editItemDetails);
    }

    public function hideEditModal() 
    {   
        $this->duplicateInput = false;
        $this->showEditModal = false;
        $this->reset('editItemDetails');
        $this->reset('deleteItem');
        $this->resetValidation();
    }


    public $approveModal = false;
    public $approveId;

    public function openApproveModal($id)
    {
        $this->approveId = $id;
        $this->approveModal = true;
    }
    public function hideApproveModal()
    {
        $this->approveModal = false;
        $this->reset('approveId');
    }
    
    public function approvePlan()
    {
        Plandue::where('id',$this->approveId)
            ->update([
                'status' => 'approved',
            ]);
        $this->hideApproveModal();
    }
    



    public function render()
    {    
        $plands = Plandue::where('status','pending')->where('created_by', auth()->id() )->with('listitems')->get();

        return view('livewire.plan-c-r-u-d',compact('plands'));
    }
}

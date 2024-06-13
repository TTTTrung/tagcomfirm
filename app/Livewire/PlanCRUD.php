<?php

namespace App\Livewire;

use App\Imports\ListitemImport;
use App\Models\Listitem;
use App\Models\Part;
use App\Models\Plandue;
use App\Rules\ExceedsLimit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PDOException;

class PlanCRUD extends Component
{
    use WithFileUploads;
    use WithPagination; 
    public $showCreateModal = false;
    public $duedate;
    public $car;
    public $gowith;
    public $itemDetails = [
     ['customer'=>'', 'issue' => '','po' => '','pr'=>'', 'outpart' => '', 'quantity' => '', 'body' => '','ship_to'=>''],
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
            'csvFile' => ['required','mimes:csv,xlsx'],
        ]);

        $data = Excel::toArray(new ListitemImport, $this->csvFile);
       if (!empty($data) && count($data) > 0){
        $headers = $data[0][0];
        $this->itemDetails = array_fill_keys($headers, '');

        foreach ($data[0] as $row) {
        if($row[0] !== 'CUSTOMER ID.'){     
            $customer = $row[0] ?? null;
            $issue = $row[1] ?? null;
            $po = $row[2] ?? null;
            $pr = $row[3] ?? null;
            $outpart = $row[4] ?? null;
            $quantity = $row[5] ?? null;
            $body = $row[6] ?? null;
            $ship_to = $row[7] ?? null;
        
            
            $itemDetails = [
                'customer' => $customer,
                'issue' => $issue,
                'po' => $po,
                'pr'=> $pr,
                'outpart' => $outpart,
                'quantity' => $quantity,
                'body' => $body,
                'ship_to' => $ship_to,
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
        $this->itemDetails[] = ['customer'=>'', 'issue' => '','po'=> '','pr'=>'', 'outpart' => '', 'quantity' => '','body' => '','ship_to'=>''];
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
        'duedate' => 'required|date',
        'car' => 'required',
        'itemDetails.*.customer' => ['required', Rule::exists('parts','customer')],
        'itemDetails.*.issue' => 'required',
        'itemDetails.*.outpart' => ['required',
            function ($attribute, $value, $fail){
            $index = explode('.', $attribute)[1];
            $customer = $this->itemDetails[$index]['customer'];
            $outpart = $this->itemDetails[$index]['outpart'];

            // Check if the combination of customer and outpart exists in the parts table
            if (!Part::where('customer', $customer)->where('outpart', $outpart)->exists()) {
                $fail("The outpart '$outpart' does not exist for customer '$customer'.");
            }
        }],
        'itemDetails.*.quantity' => 'required|numeric',
        ],
        [
        'duedate.required' => 'DueDate is required.',
        'duedate.date' => 'Date must be a valid date.',
        'car.required' => 'DueDate is required.',
        'itemDetails.*.customer.required' => 'Customer is required.',
        'itemDetails.*.customer.required' => 'Customer does not exist.',        
        'itemDetails.*.outpart.required' => 'Outpart field is required.',
        'itemDetails.*.quantity.required' => 'Quantity field is required.',
        'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
        ]);

        foreach($this->itemDetails as $checkIssue) 
        {
            // $latestIssue = Listitem::where('issue', 'LIKE' ,"%-{$checkIssue['issue']}")->orderByDesc('issue')->first();
            // $latestNumber = $latestIssue ? (int) explode('-', $latestIssue->issue)[0] : 0;

            $maxQuantity = Part::where('customer', $checkIssue['customer'])->where('outpart',$checkIssue['outpart'])->first();
            $snp = $maxQuantity->snp ?? null;
            // dd($checkIssue);
            $baseBody = $checkIssue['body'];
            if ($snp && $checkIssue['quantity'] > $snp) {
                $setNeeded = ceil($checkIssue['quantity'] / $snp);
                for ($i = 1; $i <= $setNeeded; $i++) {
                    // $count = $latestNumber + $i;
                    // dd($checkIssue['quantity']);
                    $quantityToAdd = min($checkIssue['quantity'], $snp); 
                    if (is_numeric($baseBody)) {
                        $finalBody = $baseBody + $quantityToAdd - 1 ?? null;
                        $body = $finalBody != null ? "{$baseBody}-{$finalBody}" : $baseBody;
                    }
                    $this->itemDetails[] = [
                        'customer' => $this->itemDetails[0]['customer'],
                        'issue' => "{$this->itemDetails[0]['issue']}",
                        'po' => $this->itemDetails[0]['po'],
                        'pr'=> $this->itemDetails[0]['pr'],
                        'outpart' => $this->itemDetails[0]['outpart'],
                        'quantity' => $quantityToAdd,
                        'body' => $body ?? $baseBody,
                        'ship_to' => $this->itemDetails[0]['ship_to'],
                    ];
                    $checkIssue['quantity'] -= $quantityToAdd;
                    if (is_numeric($baseBody))
                    {
                        $baseBody = $finalBody + 1 ?? null; 
                    }
                }
                array_splice($this->itemDetails, 0,1);
            }
            else{
                if (is_numeric($baseBody)) {
                            $finalBody = $baseBody + $checkIssue['quantity'] - 1 ?? null;
                            $body = $finalBody != null ? "{$baseBody}-{$finalBody}" : $baseBody;
                        }
                $this->itemDetails[] = [
                            'customer' => $this->itemDetails[0]['customer'],
                            'issue' => $this->itemDetails[0]['issue'],
                            'po' => $this->itemDetails[0]['po'],
                            'pr'=> $this->itemDetails[0]['pr'],
                            'outpart' => $this->itemDetails[0]['outpart'],
                            'quantity' => $this->itemDetails[0]['quantity'],
                            'body' => $body ?? $baseBody,
                            'ship_to' => $this->itemDetails[0]['ship_to'],
                        ];
                array_splice($this->itemDetails, 0, 1); 
            }
            // else
            // {
            //     if (is_numeric($baseBody)) {
            //         $finalBody = $baseBody + $checkIssue['quantity'] - 1 ?? null;
            //         $body = $finalBody != null ? "{$baseBody}-{$finalBody}" : $baseBody;
            //     }
            //     $this->itemDetails[] = [
            //         'customer' => $this->itemDetails[0]['customer'],
            //         'issue' => $latestIssue > 0 ? ($latestIssue + 1) . "-{$this->itemDetails[0]['issue']}" : $this->itemDetails[0]['issue'],
            //         'po' => $this->itemDetails[0]['po'],
            //         'outpart' => $this->itemDetails[0]['outpart'],
            //         'quantity' => $this->itemDetails[0]['quantity'],
            //         'body' => $body ?? $baseBody,
            //         'ship_to' => $this->itemDetails[0]['ship_to'],
            //     ];
            //     array_splice($this->itemDetails, 0, 1);
            // }          
        }
    }

    public function createPlan()
    {
        // $this->duplicateInput = false;

        // if (count(array_column($this->itemDetails, 'issue')) !== count(array_unique(array_column($this->itemDetails, 'issue')))) {
        //     $this->duplicateInput = true;
        // } 
        // else {
            $this->validate([
                'duedate' => 'required|date',
                'car' => 'required|in:4W,6W,Trailer,Station,Milk run',
                'itemDetails.*.customer' => ['required', Rule::exists('parts','customer')],
                'itemDetails.*.issue' => ['required',
                // function ($attribute, $value,$fail){
                //     $index = explode('.', $attribute)[1];
                //     $customer = $this->itemDetails[$index]['customer'];
                //     $issue = $this->itemDetails[$index]['issue'];

                //     if(Listitem::where('customer', $customer)->where('issue', $issue)->exists()){
                //         $fail("The issue is duplicate");
                //     }
                // }
                // function ($attribute, $value, $fail) {
                //     $finds = ['[issue]', '[lot]', '[line]', '[serial]'];
                //     $found = false;
            
                //     foreach ($finds as $find) {
                //         if (strpos($value, $find) === 0) {
                //             $found = true;
                //             break;
                //         }
                //     }
            
                //     if (!$found) {
                //         $fail('The value must start  with one of the following prefixes: ' . implode(', ', $finds) . '.');
                //     }
                // }
                ],
                'itemDetails.*.po' => 'required',
                'itemDetails.*.outpart' => ['required',
                function ($attribute, $value, $fail){
                    $index = explode('.', $attribute)[1];
                    $customer = $this->itemDetails[$index]['customer'];
                    $outpart = $this->itemDetails[$index]['outpart'];
        
                    // Check if the combination of customer and outpart exists in the parts table
                    if (!Part::where('customer', $customer)->where('outpart', $outpart)->exists()) {
                        $fail("The outpart '$outpart' does not exist for customer '$customer'.");
                    }
                }
                ],
                'itemDetails.*.quantity' =>
                ['required', 'numeric', 
                function ($attribute, $value, $fail) {
                    
                    $index = explode('.', $attribute)[1];
                    $outpart = $this->itemDetails[$index]['outpart'] ?? null;
                    $limit = Part::where('customer',$this->itemDetails[$index]['customer'])->where('outpart', $outpart)->value('snp');
                      if(is_null($limit)){
                        $fail("");
                      }
        
                    if ($value > $limit) {
                        $fail("The quantity for $outpart exceeds the limit.");
                    }
                },] ,
            ],[
                'duedate.required' => 'DueDate is required.',
                'duedate.date' => 'Date must be a valid date.',
                'car.required' => 'DueDate is required.',
                'itemDetails.*.customer.required' => 'Customer field is required.',
                'itemDetails.*.customer.exists' => 'Customer does not exist.',
                'itemDetails.*.issue.required' => 'Issue field is required.',
                'itemDetails.*.issue.unique' => 'Issue must be unique.',
                'itemDetails.*.po.required' => 'PO field is required.',
                'itemDetails.*.outpart.required' => 'Outpart field is required.',
                'itemDetails.*.outpart.exists' => 'Outpart does not exist.',
                'itemDetails.*.quantity.required' => 'Quantity field is required.',
                'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
                'itemDetails.*.quantity.exceeds_limit' => 'The quantity exceeds the maximum limit for the outpart.',
            ]);
        //  try{
            $currentDate = Carbon::now()->format('Ymd');
            $latestRecord = Plandue::latest()->whereDate('created_at',$currentDate)->first();
            $counter = $latestRecord ? (int) substr($latestRecord->plan_id, -4) + 1 : 1;
            $counterFormatted = sprintf('%04d', $counter);
            $customId = "DP-{$currentDate}-{$counterFormatted}";
            $customer =  DB::connection('oracle')
            ->select("SELECT 
            hca.account_number AS CUSTOMER_NUMBER,
            hp.party_name AS CUSTOMER_NAME,
            hcsu.price_list_id
            FROM 
                AR.hz_parties hp
            JOIN 
                AR.hz_cust_accounts hca ON hp.party_id = hca.party_id
            LEFT JOIN 
                AR.hz_cust_acct_sites_all hcas ON hca.cust_account_id = hcas.cust_account_id
            LEFT JOIN 
                AR.hz_party_sites hps ON hps.party_site_id = hcas.party_site_id
            LEFT JOIN 
                AR.hz_cust_site_uses_all hcsu ON hcas.cust_acct_site_id = hcsu.cust_acct_site_id
            LEFT JOIN 
                AR.hz_locations hl ON hps.location_id = hl.location_id
            JOIN 
                APPS.hr_operating_units hou ON hcas.org_id = hou.organization_id
            WHERE 
                hp.party_type = 'ORGANIZATION' 
                AND hp.status = 'A' 
                AND hps.status = 'A' 
                AND hcas.org_id = 84 
                AND hcsu.site_use_code = 'BILL_TO' 
                AND hca.account_number = '{$this->itemDetails[0]['customer']}'
            ORDER BY 
                hp.party_name, hca.account_number") ?? null;
        
            $plan = Plandue::create([
                'plan_id' => $customId,
                'status' => 'pending',
                'company_name' => $customer[0]->customer_name ?? null,
                'duedate' => $this->duedate,
                'car' => $this->car,
                'go_with' => $this->gowith,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->itemDetails as $item) {
                $trupart = Part::where('customer',$item['customer'])->where('outpart',$item['outpart'])->first();
    

                $price_list = DB::connection('oracle')
                    ->select("SELECT 
                    hca.account_number AS CUSTOMER_NUMBER,
                    hp.party_name AS CUSTOMER_NAME,
                    hcsu.price_list_id
                FROM 
                    AR.hz_parties hp
                JOIN 
                    AR.hz_cust_accounts hca ON hp.party_id = hca.party_id
                LEFT JOIN 
                    AR.hz_cust_acct_sites_all hcas ON hca.cust_account_id = hcas.cust_account_id
                LEFT JOIN 
                    AR.hz_party_sites hps ON hps.party_site_id = hcas.party_site_id
                LEFT JOIN 
                    AR.hz_cust_site_uses_all hcsu ON hcas.cust_acct_site_id = hcsu.cust_acct_site_id
                LEFT JOIN 
                    AR.hz_locations hl ON hps.location_id = hl.location_id
                JOIN 
                    APPS.hr_operating_units hou ON hcas.org_id = hou.organization_id
                WHERE 
                    hp.party_type = 'ORGANIZATION' 
                    AND hp.status = 'A' 
                    AND hps.status = 'A' 
                    AND hcas.org_id = 84 
                    AND hcsu.site_use_code = 'BILL_TO' 
                    AND hca.account_number = {$item['customer']}
                ORDER BY 
                    hp.party_name, hca.account_number") ?? null;
               

                if (!empty($price_list)){
                   
                    
                $price = DB::connection('oracle')->select("SELECT
                    QSLH.NAME AS M_PRICE_CODE,
                    MSI.SEGMENT1 AS M_ITEM_CODE,
                    QPLL.OPERAND ,
                    QPPR.PRODUCT_ATTR_VALUE,
                    QSLH.LIST_HEADER_ID
                FROM
                    APPS.QP_SECU_LIST_HEADERS_V QSLH
                JOIN
                    QP.QP_LIST_LINES QPLL ON QSLH.LIST_HEADER_ID = QPLL.LIST_HEADER_ID
                JOIN
                    QP.QP_PRICING_ATTRIBUTES QPPR ON QPPR.LIST_LINE_ID = QPLL.LIST_LINE_ID
                JOIN
                    INV.MTL_SYSTEM_ITEMS_B MSI ON QPPR.PRODUCT_ATTR_VALUE = MSI.INVENTORY_ITEM_ID
                WHERE
                    QPLL.END_DATE_ACTIVE IS NULL
                    AND MSI.ORGANIZATION_ID = 103
                    AND MSI.SEGMENT1 = '{$trupart->trupart}'
                    AND QSLH.LIST_HEADER_ID = {$price_list[0] -> price_list_id}
                ORDER BY
                    M_ITEM_CODE") ?? 0;
                }
                // dd($item['pr']);
                Listitem::create([
                    'plandue_id' => $plan->id,
                    'created_by' => auth()->id(),
                    'customer' => $item['customer'],
                    'issue' => $item['issue'],
                    'po' => $item['po'],
                    'pr'=>$item['pr'],
                    'outpart' => $item['outpart'],
                    'prize'=>  $item['quantity'] * ($price[0]->operand ?? 0) ?? null,
                    'quantity' => $item['quantity'],
                    'body' => $item['body'],
                    'ship_to' => $item['ship_to'],
                ]);
            }
            $this->reset(['itemDetails','duedate','car']);
            $this->hideCreateModal();
            session()->flash('success', 'Plan create successfully.');
        // }catch (\Exception $e)
        //     {
        //     $this->hideCreateModal();
        //     session()->flash('error', 'An error occurred while create the plan.');  
        //     }
        // } 
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
        try{
            Plandue::where('id', $this -> selectedPlan['id'])->delete();
            $this->hideDeleteModal();
            session()->flash('success', 'Plan delete successfully.');
        }catch (\Exception $e){
            $this->hideDeleteModal();
            session()->flash('error', 'An error occurred while delete the plan.');  
        }
        
    }

    public $editItems; 
    public $showEditModal=false;
    public $editItemDetails=[];
    public $editId;
    public $countEditItems;
    public $eDuedate;
    public $eCar;
    public $egowith;
    public $deleteItem;

    public function openEditModal($id)
    {   
        $this->editId = $id;
        $this->editItems = Plandue::where('id',$this->editId)->with('listitems')->get();
        // dd($this->editItems->listitems);
        // $this->editItems = Listitem::select('id','customer','po','body','outpart','duedate','prize','issue','quantity','ship_to')->where('plandue_id',$id)->get();
        if(!empty($this->editItems))
        {
            foreach($this->editItems as $editItem)   
            {
                $this->eDuedate = $editItem->duedate;
                $this->eCar = $editItem->car;
                $this->egowith = $editItem->go_with;
                foreach($editItem->listitems as $editItem)
                {
                $this->editItemDetails[] = ['id'=>$editItem->id ,'customer' => $editItem->customer, 'duedate' => $editItem->duedate , 'issue' => $editItem->issue,'po' => $editItem->po,'pr'=>$editItem->pr, 'outpart' => $editItem->outpart, 'quantity' =>$editItem->quantity,'prize'=>$editItem->prize, 'body'=>$editItem->body,'ship_to'=>$editItem->ship_to];
                }
            }
        }
        $this->countEditItems = count($this->editItemDetails);
        $this->showEditModal = true;
    }
    
    public function editPlan()
    {
        // $this->duplicateInput = false;
        // if (count(array_column($this->editItemDetails, 'issue')) !== count(array_unique(array_column($this->editItemDetails, 'issue')))) {
        //     $this->duplicateInput = true;
        // } 

        foreach($this->editItemDetails as $index  => $item)
              
            $this->validate([
                'eDuedate' => 'required|date',
                'eCar' => 'required|in:4W,6W,Trailer,Staion,Milk run',
                "editItemDetails.$index.customer" => ["required",Rule::exists('parts','customer')],
                "editItemDetails.$index.issue" => ['required',
                // function ($attribute, $value,$fail){
                //     $index = explode('.', $attribute)[1];
                //     $customer = $this->editItemDetails[$index]['customer'];
                //     $issue = $this->editItemDetails[$index]['issue'];

                //     if(Listitem::where('customer', $customer)->where('issue', $issue)->where('id', '!=', $this->editItemDetails[$index]['id'])->exists()){
                //         $fail("The issue is duplicate");
                //     }
                // }
                ],
                "editItemDetails.$index.po" => 'required',
                "editItemDetails.$index.outpart" => ['required',
                function ($attribute, $value, $fail){
                    $index = explode('.', $attribute)[1];
                    $customer = $this->editItemDetails[$index]['customer'];
                    $outpart = $this->editItemDetails[$index]['outpart'];
            
                        // Check if the combination of customer and outpart exists in the parts table
                    if (!Part::where('customer', $customer)->where('outpart', $outpart)->exists()) {
                        $fail("The outpart '$outpart' does not exist for customer '$customer'.");
                    }
                }
                ],
                "editItemDetails.$index.quantity" => ["required", 'numeric', 
                function ($attribute, $value, $fail) {
                    
                    $index = explode('.', $attribute)[1];
                    $outpart = $this->editItemDetails[$index]['outpart'] ?? null;
                    $limit = Part::where('outpart', $outpart)->value('snp');
                      if(is_null($limit)){
                        $fail("");
                      }
        
                    if ($value > $limit) {
                        $fail("The quantity for $outpart exceeds the limit.");
                    }
                }],
                ],[
                'eDuedate.required' => 'DueDate is required.',
                'eDuedate.date' => 'Date must be a valid date.',
                'eCar.required' => 'Car is required.',
                'eCar.in' => 'Car value worng.',
                "editItemDetails.$index.issue.required" => 'Issue field is required.',
                "editItemDetails.$index.issue.unique" => 'Issue must be unique.',
                "editItemDetails.$index.outpart.required" => 'Outpart field is required.',
                "editItemDetails.$index.outpart.exists" => 'Outpart does not exist.',
                "editItemDetails.$index.quantity.required" => 'Quantity field is required.',
                "editItemDetails.$index.quantity.numeric" => 'Quantity must be numeric.',
                "editItemDetails.$index.prize.numeric" => 'Quantity must be numeric.',
                ]);  
        try{
        
            Plandue::where('id',$this->editId)->update([
                'duedate' => $this->eDuedate,
                'car' => $this->eCar,
                'go_with' => $this->egowith,
            ]);
            
        if(!empty($this->deleteItem)){   
            foreach($this->deleteItem as $deleteI){
                Listitem::where('id',$deleteI['id'])->delete();
            }
        }

        foreach($this->editItemDetails as $updateItem){
            Listitem::where('id', $updateItem['id'])->update([
                'customer' => $updateItem['customer'],
                'issue' => $updateItem['issue'],
                'po' => $updateItem['po'],
                'pr' => $updateItem['pr'],
                'outpart'=>$updateItem['outpart'],
                'quantity'=>$updateItem['quantity'],
                'prize'=> $updateItem['prize'],
                'body' => $updateItem['body'],
                'ship_to' => $updateItem['ship_to'],
                'updated_by' => auth()->id(),
                ]);
            }
            foreach(array_reverse($this->editItemDetails) as $createInUpdate){
                if(!empty($createInUpdate['id'])){
                    break;
                }
                Listitem::create([
                    'plandue_id' => $this->editId,
                    'created_by' => auth()->id(),
                    
                    'customer' => $createInUpdate['customer'],
                    'issue' => $createInUpdate['issue'],
                    'po' => $createInUpdate['po'],
                    'outpart' => $createInUpdate['outpart'],
                    'quantity' => $createInUpdate['quantity'],
                    'body' =>$createInUpdate['body'],
                ]);
            }
            $this->hideEditModal();
            session()->flash('success', 'Plan edit successfully.');
        }catch (\Exception $e){
            $this->hideEditModal();
            session()->flash('error', 'An error occurred while edit the plan.'); 
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
        $this->editItemDetails[] = ['id'=> '','customer'=>'' ,'issue' => '','po' => '','pr'=>'', 'outpart' => '', 'quantity' => '','prize' => '','body' => '','ship_to'=>''];
        $this->countEditItems = count($this->editItemDetails);
    }

    public function hideEditModal() 
    {   
        $this->duplicateInput = false;
        $this->showEditModal = false;
        $this->reset(['editItemDetails','eDuedate','eCar']);
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
        $listpland = Plandue::orderBy('id')->get();

        return view('livewire.plan-c-r-u-d',compact('plands','listpland'));
    }
}

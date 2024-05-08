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
use Maatwebsite\Excel\Facades\Excel;
use PDOException;

class PlanCRUD extends Component
{
    use WithFileUploads;
    public $showCreateModal = false;
    public $itemDetails = [
        ['duedate' => '','customer'=>'', 'issue' => '','po' => '', 'outpart' => '', 'quantity' => '', 'body' => ''],
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
        if($row[0] !== 'duedate'){     
            $duedate = $row[0];
            $customer = $row[1];
            $issue = $row[2];
            $outpart = $row[3];
            $quantity = $row[4];
        
            
            $itemDetails = [
                'duedate' => $duedate,
                'customer' => $customer,
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
        $this->itemDetails[] = ['duedate' => '','customer'=>'', 'issue' => '','po'=> '', 'outpart' => '', 'quantity' => '','body' => ''];
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
        'itemDetails.*.customer' => ['required', Rule::exists('parts','customer')],
        'itemDetails.*.issue' => 'required|string',
        'itemDetails.*.outpart' => ['required',Rule::exists('parts','outpart')],
        'itemDetails.*.quantity' => 'required|numeric',
    ],
    [
        'itemDetails.*.duedate.required' => 'DueDate is required.',
        'itemDetails.*.duedate.date' => 'Date must be a valid date.',
        'itemDetails.*.customer.required' => 'Customer is required.',
        'itemDetails.*.customer.required' => 'Customer does not exist.',        
        'itemDetails.*.outpart.required' => 'Outpart field is required.',
        'itemDetails.*.outpart.exists' => 'Outpart does not exist.',
        'itemDetails.*.quantity.required' => 'Quantity field is required.',
        'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
    ]);

    if (!empty($this->itemDetails)) {
        $currentDate = Carbon::now()->format('Ymd');
        $searchPattern = "IS-{$currentDate}-";

        foreach($this->itemDetails as $checkIssue) {
            // dd($checkIssue);
            if (empty($checkIssue['issue'])) {
                $latestIssue = Listitem::latest()->where('issue', 'LIKE', "%{$searchPattern}%")->first();
                $countIssue = $latestIssue ? (int) substr($latestIssue->issue, -4) + 1 : 1;
                $issueCounterFormatted = sprintf('%04d', $countIssue);
                $customIssue = "IS-{$currentDate}-{$issueCounterFormatted}";
                $this->itemDetails[0]['issue'] = $customIssue;
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
              
                $maxQuantity = Part::where('customer', $checkIssue['customer'])->where('outpart',$checkIssue['outpart'])->first();
                $snp = $maxQuantity->snp ?? null;
                $baseBody = $checkIssue['body'];
                if ($snp && $checkIssue['quantity'] > $snp) {
                    $setNeeded = ceil($checkIssue['quantity'] / $snp);
                    for ($i = 1; $i <= $setNeeded; $i++) {
                        $count = $latestNumber + $i;
                        $quantityToAdd = min($checkIssue['quantity'], $snp); 
                        if (is_numeric($baseBody)) {
                            $finalBody = $baseBody + $quantityToAdd - 1 ?? null;
                            $body = $finalBody != null ? "{$baseBody}-{$finalBody}" : $baseBody;
                        }
                        $this->itemDetails[] = [
                            'duedate' => $this->itemDetails[0]['duedate'],
                            'customer' => $this->itemDetails[0]['customer'],
                            'issue' => "{$count}-{$this->itemDetails[0]['issue']}",
                            'po' => $this->itemDetails[0]['po'],
                            'outpart' => $this->itemDetails[0]['outpart'],
                            'quantity' => $quantityToAdd,
                            'body' => $body ?? $baseBody,
                        ];
                        $checkIssue['quantity'] -= $quantityToAdd;
                        if (is_numeric($baseBody))
                        {
                            $baseBody = $finalBody + 1 ?? null; 
                        }
                    }
                    array_splice($this->itemDetails, 0, 1);
                }
                else
                {
                    if (is_numeric($baseBody)) {
                        $finalBody = $baseBody + $checkIssue['quantity'] - 1 ?? null;
                        $body = $finalBody != null ? "{$baseBody}-{$finalBody}" : $baseBody;
                    }
                    $this->itemDetails[] = [
                        'duedate' => $this->itemDetails[0]['duedate'],
                        'customer' => $this->itemDetails[0]['customer'],
                        'issue' => $latestIssue > 0 ? ($latestIssue + 1) . "-{$this->itemDetails[0]['issue']}" : $this->itemDetails[0]['issue'],
                        'po' => $this->itemDetails[0]['po'],
                        'outpart' => $this->itemDetails[0]['outpart'],
                        'quantity' => $this->itemDetails[0]['quantity'],
                        'body' => $body ?? $baseBody,
                    ];
                    array_splice($this->itemDetails, 0, 1);
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
                'itemDetails.*.customer' => ['required', Rule::exists('parts','customer')],
                'itemDetails.*.issue' => ['required','string',
                function ($attribute, $value,$fail){
                    $index = explode('.', $attribute)[1];
                    $customer = $this->itemDetails[$index]['customer'];
                    $issue = $this->itemDetails[$index]['issue'];

                    if(Listitem::where('customer', $customer)->where('issue', $issue)->exists()){
                        $fail("The issue is duplicate");
                    }
                }],
                'itemDetails.*.po' => 'required',
                'itemDetails.*.outpart' => ['required','string',
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
                'itemDetails.*.po.required' => 'PO field is required.',
                'itemDetails.*.outpart.required' => 'Outpart field is required.',
                'itemDetails.*.outpart.exists' => 'Outpart does not exist.',
                'itemDetails.*.quantity.required' => 'Quantity field is required.',
                'itemDetails.*.quantity.numeric' => 'Quantity must be numeric.',
                'itemDetails.*.quantity.exceeds_limit' => 'The quantity exceeds the maximum limit for the outpart.',
            ]);
        try{
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
        }catch (PDOException $e)
        {
            $customer = null;
        }
        
            $plan = Plandue::create([
                'plan_id' => $customId,
                'status' => 'pending',
                'company_name' => $customer[0]->customer_name ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->itemDetails as $item) {
                $trupart = Part::where('customer',$item['customer'])->where('outpart',$item['outpart'])->first();
    
                try{
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
                } catch (PDOException $e){
                    $price_list = null;
                }
               

                if (!empty($price_list)){
                    try{
                    
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
                    } catch (PDOException $e){
                        $price = null;
                    }
                    

                }
                // dd($item);
                Listitem::create([
                    'plandue_id' => $plan->id,
                    'created_by' => auth()->id(),
                    'duedate' => $item['duedate'],
                    'customer' => $item['customer'],
                    'issue' => $item['issue'],
                    'po' => $item['po'],
                    'outpart' => $item['outpart'],
                    'prize'=>  $item['quantity'] * ($price[0]->operand ?? 0) ?? null,
                    'quantity' => $item['quantity'],
                    'body' => $item['body'],
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
        $this->editItems = Listitem::select('id','customer','po','body','outpart','duedate','issue','quantity')->where('plandue_id',$id)->get();
        // dd($this->editItems->toArray());
        if(!empty($this->editItems))
        {
            foreach($this->editItems as $editItem)
            {
            $this->editItemDetails[] = ['id'=>$editItem->id ,'customer' => $editItem->customer, 'duedate' => $editItem->duedate , 'issue' => $editItem->issue,'po' => $editItem->po, 'outpart' => $editItem->outpart, 'quantity' =>$editItem->quantity, 'body'=>$editItem->body];
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
                    "editItemDetails.$index.customer" => ["required","string",Rule::exists('parts','customer')],
                    "editItemDetails.$index.issue" => ['required','string',
                    function ($attribute, $value,$fail){
                        $index = explode('.', $attribute)[1];
                        $customer = $this->itemDetails[$index]['customer'];
                        $issue = $this->itemDetails[$index]['issue'];

                        if(Listitem::where('customer', $customer)->where('issue', $issue)->exists()){
                            $fail("The issue is duplicate");
                        }
                    }],
                    "editItemDetails.$index.po" => 'required',
                    "editItemDetails.$index.outpart" => ['required','string',
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

<?php

namespace App\Livewire;

use App\Models\Opriceli;
use App\Models\Ordert;
use App\Models\Part;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PartCRUD extends Component
{
    use WithPagination;
    public $selectedPartData;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $typee;
    public $partname;
    public $outpart;
    public $trupart;
    public $snp;
    public $weight;
    public $customer;
    public $wlh;
    public $ordertype;
    public $salerep;
    public $pricelist;
    public $bill_to;
    public $pName;

    public $epName;
    public $evendor;
    public $etypee;
    public $epartname;
    public $eoutpart;
    public $etrupart;
    public $esnp;
    public $eweight;
    public $ewlh;
    public $eordertype;
    public $esalerep;
    public $epricelist;
    public $ebill_to;

    public $search;
    public $company = "";

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function hideCreateModal()
    {   
        $this->showCreateModal = false;
        $this->reset(['typee','partname','outpart','trupart','snp','weight','customer','wlh','ordertype','salerep','pricelist','bill_to','pName']);
        $this->resetValidation();
    }

    public function addPart()
    {
       $this->validate([
        'customer' => 'required',
        'typee' => 'required',
        'partname' => 'required',
        'outpart' => [
            'required',
            Rule::unique('parts')->where(function ($query) {
                return $query->where('customer', $this->customer);
            }),
        ],
        'trupart' => 'required',
        'snp' => 'required|numeric|gt:0',
        'weight' => 'required|numeric|gte:0',
        'wlh' => 'required',
        'ordertype' => 'required',
        'salerep'=> 'required',
        'pricelist'=>'required',
        'bill_to'=> ['required',
            function($attribute, $value, $fail){
                $bills = DB::connection('oracle')->select("SELECT 
                hca.account_number AS CUSTOMER_CODE,
                ship_su.LOCATION
            FROM 
                hz_cust_site_uses_all ship_su
            LEFT JOIN 
                hz_cust_acct_sites_all ship_cas 
            ON 
                ship_su.cust_acct_site_id = ship_cas.cust_acct_site_id
                AND ship_su.org_id = ship_cas.org_id
            LEFT JOIN 
                AR.hz_cust_accounts hca 
            ON 
                ship_cas.cust_account_id = hca.cust_account_id
            WHERE 
                ship_su.org_id = 84
                AND ship_su.SITE_USE_CODE = 'BILL_TO'
                AND hca.account_number = '{$this->customer}'");

                $check = TRUE;
                foreach($bills as $bill) {
                    // dd($value == $bill->location);
                    if($value == $bill->location) {
                        $check = FALSE;
                        break; // Exit the loop as soon as we find a match
                    }
                }
                if ($check) {
                    $fail("The customer($this->customer) doesn't have this bill number.");
                } 
            }]
       ]);

       Part::create([
            'customer' => $this->customer,
            'type' => $this->typee,
            'partname' => $this->partname,
            'outpart' => $this->outpart,
            'trupart' => $this->trupart,
            'snp' =>    $this->snp,
            'weight' => $this->weight,
            'pl_size' => $this->wlh,
            'order_type' => $this->ordertype,
            'sale_reps' => $this->salerep,
            'price_list'=>$this->pricelist,
            'bill_to'=>$this->bill_to,
            'pallet_name' => $this->pName,
            'created_by' => auth()->id()
       ]);

       $this->hideCreateModal();
    }

    public function openEditModal($id)
    {
        $this->selectedPartData = Part::find($id);
        if($this->selectedPartData)
        {
        $this->evendor = $this->selectedPartData['customer']?? null;
        $this->etypee = $this->selectedPartData['type']?? null;
        $this->epartname = $this->selectedPartData['partname']?? null;
        $this->eoutpart = $this->selectedPartData['outpart']?? null;
        $this->etrupart = $this->selectedPartData['trupart']?? null;
        $this->esnp = $this->selectedPartData['snp'] ?? null;
        $this->eweight = $this->selectedPartData['weight'] ?? null;
        $this->ewlh = $this->selectedPartData['pl_size'] ?? null;
        $this->epName = $this->selectedPartData['pallet_name'] ?? null;
        $this->esalerep = $this->selectedPartData['sale_reps']?? null;
        $this->eordertype = $this->selectedPartData['order_type']?? null;
        $this->epricelist = $this->selectedPartData['price_list']?? null;
        $this->ebill_to = $this->selectedPartData['bill_to']?? null;
    
        $this->showEditModal = true;
        }
    }

    public function hideEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset(['etypee','epartname','eoutpart','etrupart','esnp','eweight','evendor','ewlh','eordertype','esalerep','epricelist','ebill_to']);
    }

    public function editPart()
    {
        $this->validate([
        'evendor' => 'required',
        'etypee' => 'required',
        'epartname' => 'required',
        'eoutpart' => ['required',function($attribute , $value , $fail){
            if(Part::where('customer',$this->evendor)->where('outpart',$value)->whereNot('id',$this->selectedPartData['id'])->exists()){
                $fail("out part already exist");
            }
        }],
        'etrupart' => 'required|string',
        'esnp' => 'required|numeric|gt:0',
        'eweight' => 'required|numeric|gte:0',
        'ewlh' => 'required',
        'eordertype' => 'required',
        'esalerep'=> 'required',
        'epricelist'=>'required',
        'ebill_to'=> ['required',
        function($attribute, $value, $fail){
            $bills = DB::connection('oracle')->select("SELECT 
            hca.account_number AS CUSTOMER_CODE,
            ship_su.LOCATION
        FROM 
            hz_cust_site_uses_all ship_su
        LEFT JOIN 
            hz_cust_acct_sites_all ship_cas 
        ON 
            ship_su.cust_acct_site_id = ship_cas.cust_acct_site_id
            AND ship_su.org_id = ship_cas.org_id
        LEFT JOIN 
            AR.hz_cust_accounts hca 
        ON 
            ship_cas.cust_account_id = hca.cust_account_id
        WHERE 
            ship_su.org_id = 84
            AND ship_su.SITE_USE_CODE = 'BILL_TO'
            AND hca.account_number = '{$this->evendor}'");

        // dd($bill);
        $check = TRUE;
        foreach($bills as $bill) {
            // dd($value == $bill->location);
            if($value == $bill->location) {
                $check = FALSE;
                break; // Exit the loop as soon as we find a match
            }
        }
        if ($check) {
            $fail("The customer($this->evendor) doesn't have this bill number.");
        }        
        }],
        ]);

        Part::where('id', $this->selectedPartData['id'])->update([
            
            'customer' => $this->evendor,
            'type' => $this->etypee,
            'partname' => $this->epartname,
            'outpart' => $this->eoutpart,
            'trupart' => $this->etrupart,
            'snp' =>    $this->esnp,
            'weight' => $this->eweight,
            'pl_size' => $this->ewlh,
            'order_type' => $this->eordertype,
            'sale_reps' => $this->esalerep,
            'pallet_name' => $this->epName,
            'price_list'=>$this->epricelist,
            'bill_to'=>$this->ebill_to,
            'updated_by' => auth()->id()

        ]);

        $this->hideEditModal();
    }

    public function render()
    {
        $com_id = Part::whereNotNull('customer')->distinct()->pluck('customer');
        $ordertypes = Ordert::select('name')->orderBy('name')->get();
        $salereps = DB::connection('oracle')
            ->select("SELECT 
                    rs.salesrep_id, 
                    res.resource_name
                    FROM 
                        apps.jtf_rs_salesreps rs
                    JOIN 
                        apps.jtf_rs_resource_extns_vl res
                    ON 
                        rs.resource_id = res.resource_id
                    WHERE 
                        rs.org_id = 84
                    ORDER BY
                        resource_name DESC
                        ");
        $pricelists = Opriceli::orderBy('name','desc')->get();
        // dd($salereps);
        $parts = Part::with(['createdBy','updatedBy'])
        ->when($this->company !== "", function($query){
            $query->where('customer',$this->company);
        })
        ->searchPart($this->search)->orderBy('id','desc')->paginate(10);
        return view('livewire.part-c-r-u-d',compact('parts','com_id','ordertypes','salereps','pricelists'));
    }
}

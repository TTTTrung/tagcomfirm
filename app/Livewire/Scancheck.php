<?php

namespace App\Livewire;

use App\Models\History;
use App\Models\Plandue;
use App\Models\Part;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Scancheck extends Component
{
    public $companys =['BKC','NISSAN']; 
    public $company = null;
    public $issueCheck = false;
    public $planid;
    public $scan =['issue'=> '','outside'=>'','partT'=>'','qty'=>'']; 

    public function updateCompany(){
        if($this->company == "NISSAN"){
            $this->issueCheck = true;
        }
        else{
            $this->issueCheck = false;
        }
    }


    public function scanchoice(){
       $this->validate([
            'companys' => ['required'],
            'planid' => ['required'],
            'scan.issue' => ['required_if:company,NISSAN'],
            'scan.outside' => ['required'],
            'scan.partT' => ['required'],
            'scan.qty' => ['required'],
       ]);
       if ($this->company == "BKC"){
        return $this->bkc($this->planid,$this->scan);
       }
       if($this->company == "NISSAN"){
        return $this->nissan($this->planid,$this->scan);
       }
    }

    public function bkc($id,$scan){
    $outpart = explode(',', $scan['outside']);
    $outpart = explode('_', end($outpart));

    $check = Plandue::where('plan_id', $id)->with('listitems')->first();

    $pallet = null;

    if ($check) {
        $pallet = $check->listitems()
            ->where('outpart', $outpart[1] ?? null)
            ->where('quantity', $scan['qty'])
            ->whereNull('flag')
            ->first();
    }
    if (Part::where('customer', '20062')
        ->where('outpart', $outpart[1] ?? null)
        ->where('trupart', $scan['partT'])
        ->exists() && !is_null($pallet)) {
        try{
        $pallet->update(['flag' => true]);
        History::create([
            'planid' => $id,
            'customer' => '20062',
            'outside' => $outpart[1] ?? null,// Ensure this value exists
            'thpart' => $scan['partT'],
            'qty' => $scan['qty'],
            'status' => 'success',
            'created_by' => auth()->id(),
        ]);
        $this->reset('scan');
        $this->resetValidation();
        session()->flash('success','successful scan '.($outpart[1]));
    }catch(\Exception $e){
        session()->flash('error','something went wrong');
    }
    }
    else{
        try{
        History::create([
            'planid' => $id,
            'customer' => '20062',
            'outside' => $outpart[1] ?? null,
            'thpart' => $scan['partT'],
            'qty' => $scan['qty'],
            'status' => 'fail',
            'created_by' => auth()->id()
        ]);
          $userId = Auth::id();
            $user = User::find($userId);
            $role = Role::findOrCreate('lock');
            $user->syncRoles($role);
            return redirect()->route('unlock')->with('error', 'no part or part already scan');
    }catch(\Exception $e){
        session()->flash('error','something went wrong');
    }
    }
    }
    public function nissan($id,$scan){
        $cleanedIssueno = substr($scan['issue'] ,2);
        $cleanedpartout = substr($scan['outside'] ,1);
        $cleanedQuality = (int) preg_replace('/[^\d]/', '', $scan['qty']); 

        $check = Plandue::where('plan_id', $id)->with('listitems')->first();
        // dd($cleanedpartout);
        $pallet = null; 
        if ($check) {
            $pallet = $check->listitems
            ->where('outpart', $cleanedpartout ?? null)
            ->where('quantity', $cleanedQuality)
            ->where('issue',$cleanedIssueno)
            ->whereNull('flag')
            ->first(); 
        }
        if(Part::where('customer', '20146')
        ->where('outpart', $cleanedpartout ?? null)
        ->where('trupart', $scan['partT'])
        ->exists() && !is_null($pallet)){
            try{
        $pallet->update(['flag' => true]);
        History::create([
            'planid' => $id,
            'customer' => '20146',
            'issue' => $cleanedIssueno,
            'outside' => $cleanedpartout ?? null,// Ensure this value exists
            'thpart' => $scan['partT'],
            'qty' => $cleanedQuality,
            'status' => 'success',
            'created_by' => auth()->id(),
        ]);
        $this->reset('scan');
        $this->resetValidation();
        session()->flash('success','successful scan '.($cleanedpartout));
        }catch(\Exception $e){
            session()->flash('error','something went wrong');
        } 
        }
        else{
        try{
        History::create([
            'planid' => $id,
            'customer' => '20146',
            'issue' => $cleanedIssueno,
            'outside' => $cleanedpartout ?? null,
            'thpart' => $scan['partT'],
            'qty' => $cleanedQuality,
            'status' => 'fail',
            'created_by' => auth()->id()
        ]);
          $userId = Auth::id();
            $user = User::find($userId);
            $role = Role::findOrCreate('lock');
            $user->syncRoles($role);
            return redirect()->route('unlock')->with('error', 'no part or part already scan');
    }catch(\Exception $e){
        session()->flash('error','something went wrong');
    }
    }
    }
    public function clear(){
        $this->reset('company','planid','scan');
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.scancheck',['issueCheck' => $this->issueCheck]);
    }
}

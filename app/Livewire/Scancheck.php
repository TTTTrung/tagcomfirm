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
    public $companys =['BKC','NISSAN','ISUZU']; 
    public $company = null;
    public $thisCompany = "BKC";
    public $planid;
    public $scan =['issue'=> '','outside'=>'','partT'=>'','qty'=>'']; 

    public function updateCompany(){
        if($this->company == "NISSAN"){
            $this->thisCompany = "NISSAN";
        }
        elseif($this->company == "BKC"){
            $this->thisCompany = "BKC";
        }
        else{
            $this->thisCompany = "ISUZU";
        }
    }

    public function scanchoice(){
       if ($this->company == "BKC"){
        return $this->bkc(trim($this->planid),$this->scan);
       }
       if($this->company == "NISSAN"){
        return $this->nissan(trim($this->planid),$this->scan);
       }
       if($this->company == "ISUZU"){
        return $this->isuzu(trim($this->planid), $this->scan);
       }
    }

    public function bkc($id,$scan){
        dd('test1');
        $this->validate([
            'companys' => ['required'],
            'planid' => ['required'],
            'scan.outside' => ['required'],
            'scan.partT' => ['required'],
            'scan.qty' => ['required'],
       ]);
        dd('test2');
    $outpart = explode(',', $scan['outside']);
    $outpart = explode('_', end($outpart));

    $check = Plandue::where('plan_id', trim($id))->with('listitems')->first();

    $pallet = null;

    if ($check) {
        $pallet = $check->listitems()
            ->where('outpart', trim($outpart[1]) ?? null)
            ->where('quantity', trim($scan['qty']))
            ->whereNull('flag')
            ->first();
    }
    if (Part::where('customer', '20062')
        ->where('outpart', trim($outpart[1]) ?? null)
        ->where('trupart', trim($scan['partT']))
        ->exists() && !is_null($pallet)) {
        try{
        $pallet->update(['flag' => true]);
        History::create([
            'planid' => $id,
            'customer' => '20062',
            'outside' => trim($outpart[1]) ?? null,// Ensure this value exists
            'thpart' => trim($scan['partT']),
            'qty' => trim($scan['qty']),
            'status' => 'success',
            'created_by' => auth()->id(),
        ]);
        $this->reset('scan');
        $this->resetValidation();
        return session()->flash('success','successful scan '.($outpart[1]));
    }catch(\Exception $e){
        return session()->flash('error','something went wrong');
    }
    }
    else{
        try{
        History::create([
            'planid' => $id,
            'customer' => '20062',
            'outside' => trim($outpart[1]) ?? null,
            'thpart' => trim($scan['partT']),
            'qty' => trim($scan['qty']),
            'status' => 'fail',
            'created_by' => auth()->id()
        ]);
          $userId = Auth::id();
            $user = User::find($userId);
            $role = Role::findOrCreate('lock');
            $user->syncRoles($role);
            return redirect()->route('unlock')->with('error', 'no part or part already scan');
    }catch(\Exception $e){
        return session()->flash('error','something went wrong');
    }
    }
    }
    public function nissan($id,$scan){
        $this->validate([
            'companys' => ['required'],
            'planid' => ['required'],
            'scan.issue' => ['required_if:company,NISSAN'],
            'scan.outside' => ['required'],
            'scan.partT' => ['required'],
            'scan.qty' => ['required'],
       ]);
        $cleanedIssueno = trim(substr($scan['issue'] ,2));
        $cleanedpartout = trim(substr($scan['outside'] ,1));
        $cleanedQuality = (int) trim(preg_replace('/[^\d]/', '', $scan['qty'])); 

        $check = Plandue::where('plan_id', $id)->with('listitems')->first();
        $pallet = null; 
        if ($check) {
            $pallet = $check->listitems
            ->where('outpart', $cleanedpartout)
            ->where('quantity', $cleanedQuality)
            ->where('issue',$cleanedIssueno)
            ->whereNull('flag')
            ->first(); 
        }
        if(Part::where('customer', '20146')
        ->where('outpart', $cleanedpartout ?? null)
        ->where('trupart', trim($scan['partT']))
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
                return session()->flash('success','successful scan '.($cleanedpartout));
            }catch(\Exception $e){
                return session()->flash('error','something went wrong');
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

    public function isuzu($id,$outSide){
        $this->validate([
            'companys' => ['required'],
            'planid' => ['required'],
            'scan.outside' => ['required'],
            'scan.partT' => ['required'],
       ]);
        $scan = trim($outSide['outside']);
        $po =substr($scan,0,10);
        $quantity = substr($scan,14,5);
        $part = substr($scan,33,10);

        $check = Plandue::where('plan_id',$id)->with('listitems')->first();
        $pallet = null;
        if($check){
            $pallet = $check->listitems
                ->where('po',$po)
                ->where('outpart',$part)
                ->where('quantity',$quantity)
                 ->whereNull('flag')
                ->first();
        }
        if (Part::where('outpart', $part ?? null)
        ->where('trupart', $outSide['partT'])
        ->exists() && !is_null($pallet)) {
            try{
                $pallet->update(['flag' => true]);
                History::create([
                    'planid' => $id,
                    'customer' => 'ISUZU',
                    'outside' => $part ,// Ensure this value exists
                    'thpart' => $outSide['partT'],
                    'qty' => $quantity,
                    'status' => 'success',
                    'created_by' => auth()->id(),
                ]);
                $this->reset('scan');
                $this->resetValidation();
                return session()->flash('success','successful scan');
            }catch(\Exception $e){
                return session()->flash('error','something went wrong');
            }
        }
        else{
            try{
                History::create([
                    'planid' => $id,
                    'customer' => 'ISUZU',
                    'outside' => $part ?? null,
                    'thpart' => $outSide['partT'],
                    'qty' => $quantity,
                    'status' => 'fail',
                    'created_by' => auth()->id()
                ]);
                $userId = Auth::id();
                    $user = User::find($userId);
                    $role = Role::findOrCreate('lock');
                    $user->syncRoles($role);
                    return redirect()->route('unlock')->with('error', 'no part or part already scan');
            }catch(\Exception $e){
                return session()->flash('error','something went wrong');
            }
        }
    }
    public function clear(){
        $this->reset('company','planid','scan');
        $this->resetValidation();
    }
    public function render()
    {
        // dump($this->isuzu(1,1));
        return view('livewire.scancheck',['thisCompany' => $this->thisCompany]);
    }
}

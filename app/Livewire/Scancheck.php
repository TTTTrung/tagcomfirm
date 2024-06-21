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
    public $companys =['BKG']; 
    public $company;
    public $planid;
    public $scan =['outside'=>'','partT'=>'','qty'=>'']; 
    public function scanchoice(){
        // dd($this->scan);
       $this->validate([
            'companys' => ['required'],
            'planid' => ['required'],
            'scan.outside' => ['required'],
            'scan.partT' => ['required'],
            'scan.qty' => ['required']
       ]);
       if ($this->company == "BKG"){
        // dd('test');
        return $this->bkc($this->planid,$this->scan);
       }
       dd('fail');
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
    // dd(Part::where('customer', '20062')
    //     ->where('outpart', $scan['outside'])
    //     ->where('trupart', $scan['partT'])
    //     ->get());
    // dd($scan['partT']);
    if (Part::where('customer', '20062')
        ->where('outpart', $outpart[1])
        ->where('trupart', $scan['partT'])
        ->exists() && !is_null($pallet)) {
        $pallet->update(['flag' => true]);
            try{
        History::create([
            'planid' => $id,
            'customer' => '20062',
            'outside' => $outpart[1] ?? null, // Ensure this value exists
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
    public function clear(){
        $this->reset('company','planid','scan');
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.scancheck');
    }
}

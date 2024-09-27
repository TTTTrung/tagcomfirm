<?php

namespace App\Livewire;

use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Unlock extends Component
{
    public $email;
    public $password;
    public $description;
    public function unlock(){
        $this->validate([
            'email' => ['required','email'],
            'password' => ['required'],
            'description' => ['required']
        ]);
        try{
            $user = User::where('email',$this->email)->first();
        }catch(\Exception $e){
            return session()->flash('error','user or password is incorrect.'); 
        }
        if ($user && Hash::check($this->password,$user->password) && in_array('plSuperAdmin',$user->getRoleNames()->toArray()) || in_array('superAdmin',$user->getRoleNames()->toArray())|| in_array('unlocker',$user->getRoleNames()->toArray()) )
        {
            try{ 
            $history = History::where('created_by',auth()->id())->latest()->take(1)->first();
            $history->update([
                'description'=> $this->description,
                'updated_by'=> $user->id,
                 ]);
            $userId = Auth::id();
            $user = User::find($userId);
            $user->syncRoles('scanner');
            return redirect()->route('scanconfirm')->with('success','unlock user success');
         }catch(\Exception $e){
             return session()->flash('error','somthing went wrong');
         }
        }
        else{
            return session()->flash('erro','user or password is incorrect');
        }
        
    }
    public function render()
    {
        return view('livewire.unlock');
    }
}

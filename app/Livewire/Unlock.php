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
        $user = User::where('email',$this->email)->first();
        if ($user && Hash::check($this->password,$user->password) && in_array('plSuperAdmin',$user->getRoleNames()->toArray()) || in_array('superAdmin',$user->getRoleNames()->toArray()) )
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
             session()->flash('error','somthing went wrong');
         }
        }
        else{
            session()->flash('erro','user or password incorrect');
        }
        
    }
    public function render()
    {
        return view('livewire.unlock');
    }
}

<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CRUDuser extends Component
{
    use WithPagination;
    public $showCreateModal = false;
    public $name;
    public $email;
    public $password;
    public $selectedRoles = [];

    public $showEditModal = false;
    public $edEmail;
    public $edName;
    public $edSelectedRoles;
    public $selectedUser;

    public $showPasswordModal = false;
    public $newPassword;
    public $conPassword;

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function hideCreateModal()
    {
        
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password', 'selectedRoles']);
        $this->resetValidation();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Assign selected roles to the newly created user
        $user->assignRole($this->selectedRoles);

        // Reset the input fields after creating the user
        $this->hideCreateModal();
    }

    public function openEditModal($id)
    {
        $this->selectedUser = User::find($id);
        if($this->selectedUser)
        {

        $this->edEmail = $this->selectedUser->email;
        $this->edName = $this->selectedUser->name;
        $this->showEditModal = true;
        foreach($this->selectedUser->getRoleNames() as $role){
            $this->edSelectedRoles = $role;
        }
    
        }
        
        
        
        
    }
    public function hideEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['selectedUser','edEmail','edName','edSelectedRoles']);
        $this->resetValidation();
    }
    public function editUser()
    {
        $this->validate([
            'edEmail' => ['required', 'email', Rule::unique('users','email')->ignore($this->selectedUser->id)],
            'edName' => ['required'],
            'edSelectedRoles' => ['required']
        ]);
        User::where('id',$this->selectedUser->id)->update([
            'email' => $this->edEmail,
            'name' => $this->edName,
        ]);
        $this->selectedUser->syncRoles([$this->edSelectedRoles]);
        $this->hideEditModal();
    }
    public function openPasswordModal($id)
    {
        $this->selectedUser = User::find($id);
        $this->showPasswordModal = true;
    }
    public function hidePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->reset(['selectedUser','newPassword','conPassword']);
        $this->resetValidation();
    }
    public function changePassword()
    { 
        dd('test');
        $this->validate([
            'newPassword' => ["required","min:6"],
            'conPassword' => ["required","same:newPassword"]
        ],
        [
            'newPassword.required' => "This field is required.",
            'newPassword.min' => "This field must be at least 6 characters.",
            'conPassword.required' => "This field is required.",
            'conPassword.same' => 'The password must match each other.'
        ]
        );
        $passwordHash = Hash::make($this->newPassword);

        User::where('id',$this->selectedUser->id)->update([
            'password' =>  $passwordHash,
        ]);
        $this->hidePasswordModal();
       
    }
    
    public function render()
    {
        $users = User::whereDoesntHave('roles',function (Builder $roleQuery){
            $roleQuery->where('name', 'superAdmin');
        })->paginate(10);

        return view('livewire.c-r-u-duser',compact('users'));
    }
}

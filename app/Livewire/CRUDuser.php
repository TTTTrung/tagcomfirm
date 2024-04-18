<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CRUDuser extends Component
{
    public $showCreateModal = false;
    public $name;
    public $email;
    public $password;
    public $selectedRoles = [];

    public $showEditModal = false;
    public $edEmail;
    public $edName;
    public $edPassword;
    public $edselectedRoles;
    public $selectedUser;

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
        $this->edEmail = $this->selectedUser->email;
        $this->edName = $this->selectedUser->name;
        $this->showEditModal = true;
    }
    public function render()
    {
        $users = User::get();
        return view('livewire.c-r-u-duser',compact('users'));
    }
}

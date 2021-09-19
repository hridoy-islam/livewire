<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ListUsers extends Component
{
    public $state = [];
    public $showEditModal = false;
    public $user;

    public $userIdRemoved;

    public function addNew(){
        $this->state = [];
        $this->showEditModal = false;
        $this->dispatchBrowserEvent('show-form');
    }

    public function createUser(){

        $validatedData =  Validator::make($this->state, [
            'name' => 'required|min:6',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ])->validate();

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);
        session()->flash('success', 'User Created Successfully');
        $this->dispatchBrowserEvent('hide-form', ['message' => 'User Created Successfully']);

    }

    public function edit(User $user){
        $this->user = $user;
        $this->state = $user->toArray();
        $this->showEditModal = true;
        $this->dispatchBrowserEvent('show-form');

    }

    public function updateUser(){

        $validatedData =  Validator::make($this->state, [
            'name' => 'required|min:6',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|confirmed',
        ])->validate();

        if(!empty($validatedData['password'])){
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $this->user->update($validatedData);

        $this->dispatchBrowserEvent('hide-form', ['message' => 'User Updated Successfully']);
    }

    public function distroy($userid){
        $this->userIdRemoved = $userid;

        $this->dispatchBrowserEvent('delete-form');
    }

    public function delete(){


        $user = User::findOrFail($this->userIdRemoved);

        $user->delete();

        $this->dispatchBrowserEvent('hide-delete-form',  ['message' => 'User Deleted Successfully']);

    }


    public function render()
    {
        $users = User::latest()->paginate();
        return view('livewire.admin.users.list-users', ['users' => $users]);
    }
}

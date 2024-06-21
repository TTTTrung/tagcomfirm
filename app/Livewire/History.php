<?php

namespace App\Livewire;

use App\Models\History as ModelsHistory;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
   use WithPagination; 

    public function render()
    {   $historys = ModelsHistory::with(['createdBy','updatedBy'])->paginate(10);
        return view('livewire.history',compact('historys'));
    }
}

<?php

namespace App\Livewire;

use App\Models\Part;
use Livewire\Component;

class PartCRUD extends Component
{
    public $selectedPartData;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $typee;
    public $partname;
    public $outpart;
    public $trupart;
    public $snp;
    public $weight;


    public $etypee;
    public $epartname;
    public $eoutpart;
    public $etrupart;
    public $esnp;
    public $eweight;

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function hideCreateModal()
    {   
        $this->showCreateModal = false;
        $this->reset(['typee','partname','outpart','trupart','snp','weight']);
        $this->resetValidation();
    }

    public function addPart()
    {
       $this->validate([
        'typee' => 'required|string',
        'partname' => 'required|string',
        'outpart' => 'required|string|unique:parts,outpart',
        'trupart' => 'required|string',
        'snp' => 'required|numeric|gt:0',
        'weight' => 'required|numeric|gte:0'
       ]);

       Part::create([
            'type' => $this->typee,
            'partname' => $this->partname,
            'outpart' => $this->outpart,
            'trupart' => $this->trupart,
            'snp' =>    $this->snp,
            'weight' => $this->weight,
            'created_by' => auth()->id()
       ]);

       $this->hideCreateModal();
    }

    public function openEditModal($id)
    {
        $this->selectedPartData = Part::find($id);

        $this->etypee = $this->selectedPartData['type']?? null;
        $this->epartname= $this->selectedPartData['partname']?? null;
        $this->eoutpart= $this->selectedPartData['outpart']?? null;
        $this->etrupart= $this->selectedPartData['trupart']?? null;
        $this->esnp= $this->selectedPartData['snp']?? null;
        $this->eweight= $this->selectedPartData['weight']?? null;
        
        $this->showEditModal = true;
    }

    public function hideEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset(['etypee','epartname','eoutpart','etrupart','esnp','eweight']);
    }

    public function editPart()
    {
        $this->validate([
        'etypee' => 'required|string',
        'epartname' => 'required|string',
        'eoutpart' => 'required|string|unique:parts,outpart,' . $this->selectedPartData['id'],
        'etrupart' => 'required|string',
        'esnp' => 'required|numeric|gt:0',
        'eweight' => 'required|numeric|gte:0'
        ]);

        Part::where('id', $this->selectedPartData['id'])->update([
            'type' => $this->etypee,
            'partname' => $this->epartname,
            'outpart' => $this->eoutpart,
            'trupart' => $this->etrupart,
            'snp' =>    $this->esnp,
            'weight' => $this->eweight,
            'updated_by' => auth()->id()

        ]);

        $this->hideEditModal();
    }

  

    


    public function render()
    {
        $parts = Part::with(['createdBy','updatedBy'])->paginate(10);
        return view('livewire.part-c-r-u-d',compact('parts'));
    }
}

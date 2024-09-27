<?php

namespace App\Livewire;

use App\Exports\ScanHistory;
use App\Models\History as ModelsHistory;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class History extends Component
{
   use WithPagination; 
   public $showExportHistory = false;
   public $fromDate;
   public $toDate;
   public function openExport(){

        $this->showExportHistory = true;

   }
   public function closeExport(){

        $this->showExportHistory = false;

   }

    public function exportHistory(){
        $this->validate([
            'fromDate' => ['required','date'],
            'toDate' => ['required','date','after:fromDate']
        ]);
        $historys =  ModelsHistory::
        whereDate('created_at','>=',$this->fromDate)
        ->whereDate('created_at','<=', $this->toDate)
        ->get();
        // dd($historys);
        return Excel::download(new ScanHistory($historys),'history.xlsx');

    }
    public function render()
    {   
        $historys = ModelsHistory::with(['createdBy','updatedBy'])->orderBy('id','desc')->paginate(10);
        return view('livewire.history',compact('historys'));
    }
}

<?php

namespace App\Livewire;

use App\Exports\PlandueExport;
use App\Exports\TagsExport;
use App\Models\Plandue;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Approvedplan extends Component
{

    use WithPagination;

    #[Url(history:true)]
    public $search = "";

    #[Url(history:true)]
    public $mydata = "";
    
    public function export($id) 
    {
        $exportplan = Plandue::where('id', $id)->with('listitems')->with('createBy')->first();
        $test = $exportplan->listitems;
        $test2 = $exportplan->createBy;
        // dd($test2);
        if (!$exportplan) {
            return response()->json(['error' => 'Plandue not found'], 404);
        }
        // download(new PlandueExport($exportplan,$test,$test2), 'plandue.xlsx')
        return Excel::download(new TagsExport($exportplan, $test, $test2),'tag.xlsx');
    }

    public function export2($id)
    {
        $exportplan = Plandue::where('id', $id)->with('listitems')->with('createBy')->first();
        $test = $exportplan->listitems;
        $test2 = $exportplan->createBy;
        if (!$exportplan) {
            return response()->json(['error' => 'Plandue not found'], 404);
        }
        return Excel::download(new PlandueExport($exportplan,$test,$test2), 'plandue.xlsx');
    }


    public function render()
    {
        $plands = Plandue::where('status','approved')
        ->with('listitems')->when($this->mydata !== "", function($query){
            $query->where('created_by',auth()->id());
        })
            ->searchpland($this->search)
            ->paginate(10);

        return view('livewire.approvedplan',compact('plands'));
    }
}

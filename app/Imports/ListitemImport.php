<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ListitemImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {         
           return [
                'duedate' => $rows[0],
                'issue' => $rows[1],
                'outpart' => $rows[2],
                'quantity' => $rows[3],
           ];        
    }
}

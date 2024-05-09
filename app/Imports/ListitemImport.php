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
                'customer' => $rows[1],
                'issue' => $rows[2],
                'po' => $rows[3],
                'outpart' => $rows[4],
                'quantity' => $rows[5],
                'body' => $rows[6],
           ];        
    }
}

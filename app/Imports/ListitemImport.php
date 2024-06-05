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
                'customer' => $rows[0],
                'issue' => $rows[1],
                'po' => $rows[2],
                'pr' => $rows[3],
                'outpart' => $rows[3],
                'quantity' => $rows[4],
                'body' => $rows[5],
                'ship_to' =>$rows[6]
           ];        
    }
}

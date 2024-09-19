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
                'outpart' => $rows[4],
                'quantity' => $rows[5],
                'body' => $rows[6],
                'ship_to' =>$rows[7],
                'remark' =>$rows[8]
           ];        
    }
}

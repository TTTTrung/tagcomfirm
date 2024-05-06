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
                'outpart' => $rows[3],
                'quantity' => $rows[4],
           ];        
    }
}

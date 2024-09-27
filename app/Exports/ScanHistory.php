<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScanHistory implements  WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $historys;
    public function __construct($historys){

        $this->historys = $historys;

    }
    public function headings(): array
    {
        return [
        'Plan Id',
        'Customer Name',
        'Customer Part',
        'Thairung Part',
        'Issue',
        'Quantity',
        'Status',
        'Scan By',
        'Approve By',
        'Description'];
    } 
    public function styles(Worksheet $sheet)
    {
        $column = 2;
        foreach($this->historys as $history){
            $sheet->setCellValue("A$column",$history->planid ?? null);
            $sheet->setCellValue("B$column",$history->customer ?? null);
            $sheet->setCellValue("C$column",$history->outside ?? null);
            $sheet->setCellValue("D$column",$history->thpart ?? null);
            $sheet->setCellValue("E$column",$history->issue ?? null);
            $sheet->setCellValue("F$column",$history->qty ?? null);
            $sheet->setCellValue("G$column",$history->status);
            $sheet->setCellValue("H$column",$history->createdBy->name);
            $sheet->setCellValue("I$column",$history->updatedBy->name ?? null);
            $sheet->setCellValue("J$column",$history->description ?? null);
            $column += 1;
        }

    }
}

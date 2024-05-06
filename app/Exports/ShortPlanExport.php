<?php

namespace App\Exports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShortPlanExport implements WithHeadings,WithEvents,WithStyles,WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    protected $test;
    protected $test2;
    protected $sumvalue;

    public function __construct($data,$test,$test2,$sumvalue)
    {
        $this->data = $data;
        $this->test =  $test;
        $this->test2 = $test2;
        $this->sumvalue = $sumvalue;
    }

    public function startCell(): string
    {
        return 'A6';
    } 
    
    public function headings(): array
    {
        return ["ลำดับที่","ชนิดของ","ปริมาณ","SNP","น้ำหนัก","น้ำหนักรวม","จำนวนหีบห่อ","ราคา/หน่วย","ราคารวม"];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(6)->getFont()->setBold(true);
        $sheet->setCellValue("A7","(No.)");
        $sheet->setCellValue("B7","(Type)");
        $sheet->setCellValue("C7","(Q'ty)");
        $sheet->setCellValue("E7","(SNP)");
        $sheet->setCellValue("F7","(KG.)");
        $sheet->setCellValue("G7","(Pallet)");
        $sheet->setCellValue("H7","(Bath)");
        $sheet->setCellValue("I7","(Bath)");
        $currentrow = 8;

        foreach ($this->sumvalue as $index => $outpart){
        $type = Part::where("outpart",$outpart->outpart)->where("customer",$outpart->customer)->first();
        $sheet->setCellValue("A{$currentrow}",$index + 1);
        $sheet->setCellValue("B{$currentrow}","{$outpart->outpart} {$type->type}");
        $sheet->setCellValue("C{$currentrow}",$outpart->total_quantity);
        $sheet->setCellValue("D{$currentrow}",$type->snp);
        $sheet->setCellValue("E{$currentrow}",$type->weight);
        $sheet->setCellValue("F{$currentrow}",$outpart->total_quantity * $type->weight);
        $sheet->setCellValue("G{$currentrow}",ceil($outpart->total_quantity / $type->snp));
        $sheet->setCellValue("H{$currentrow}", $outpart->total_price / $outpart->total_quantity);
        $sheet->setCellValue("I{$currentrow}", $outpart->total_price);
        $currentrow += 1;
        }
    }
    
  
   
    public function registerEvents(): array
    {
        return[ 
            AfterSheet::class => function(AfterSheet $event) {
            // Set paper size to A4
            $event->sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            // Set print margins (units are in inches)
            $event->sheet->getPageMargins()->setTop(0.5);
            $event->sheet->getPageMargins()->setBottom(0.5);
            $event->sheet->getPageMargins()->setLeft(0.5);
            $event->sheet->getPageMargins()->setRight(0.5);
         },
            ];
    }
}

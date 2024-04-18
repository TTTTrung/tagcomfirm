<?php

namespace App\Exports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlandueExport implements WithHeadings , WithCustomStartCell,WithStyles,WithEvents
{
    use Exportable;

    protected $data;
    protected $test;
    protected $test2;

    public function __construct($data,$test,$test2)
    {
        $this->data = $data;
        $this->test = $test;
        $this->test2 = $test2;
    }

    // public function collection()
    // {
    //     return $this->test;
    // }

    public function map($test): array
    {
        return[
            $test->duedate,
            $test->issue,
            $test->outpart,
            $test->quantity,

        ];
    }

    public function headings(): array
    {
        return['Due date','Customer','Issue No.','Part No.','Part name','Quantity','JOB','Weight','Remark'];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(6)->getFont()->setBold(true);
        $sheet->getStyle('A6:I6')->getBorders()->getAllborders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension('6')->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(9);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(9);
        $sheet->getColumnDimension('I')->setWidth(20);

       
        
        $sheet->setShowGridlines(false);
        $sheet->setCellValue('A2','PlanDue ID :');
        $sheet->setCellValue('B2',$this->data->plan_id);
        $sheet->setCellValue('A3','Create By :');
        $sheet->setCellValue('B3',$this->test2->name);

        $currentRow = 7;
        
        foreach($this->test as $t)
        {
            $weight = Part::where('outpart',$t->outpart)->first();
            $sheet->setCellValue("A{$currentRow}",$t->duedate);
            $sheet->setCellValue("B{$currentRow}",$weight->type);
            $sheet->setCellValue("C{$currentRow}",$t->issue);
            $sheet->setCellValue("D{$currentRow}",$t->outpart);
            $sheet->setCellValue("E{$currentRow}",$weight->partname);
            $sheet->setCellValue("F{$currentRow}",$t->quantity);
            $sheet->setCellValue("G{$currentRow}",'JOB');
            $sheet->setCellValue("H{$currentRow}",'Weight');
            $sheet->setCellValue("I{$currentRow}",'Remark');

            for ($col = 'A'; $col <= 'I'; $col++) {
                $sheet->getStyle("{$col}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$col}{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$col}{$currentRow}")->getBorders()->getAllborders()->setBorderStyle(Border::BORDER_THIN);
            }

            $currentRow += 1;

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

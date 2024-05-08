<?php

namespace App\Exports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportMultipleSheetPlan implements WithMultipleSheets
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
        $this->test = $test;
        $this->test2 = $test2;
        $this->sumvalue = $sumvalue;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new class($this->data,$this->test,$this->test2) implements WithHeadings ,WithCustomStartCell ,WithStyles,WithEvents
        {
            protected $data;
            protected $test;
            protected $test2;
            public function __construct($data,$test,$test2)
            {
                $this->data = $data;
                $this->test = $test;
                $this->test2 = $test2;
            }
           
            public function headings(): array
            {
                return ['Due date', 'Customer', 'Part No.', 'Part name', 'Quantity', 'Issue No.', 'JOB', 'Weight', 'Remark','Body'];
            }

            public function startCell(): string
            {
                return 'A6';
            }

            public function styles(Worksheet $sheet)
            { 
                $sheet->getStyle(6)->getFont()->setBold(true);
                $sheet->getStyle('A6:J6')->getBorders()->getAllborders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension('6')->setRowHeight(20);
                $sheet->getColumnDimension('A')->setWidth(16);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(9);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(9);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('j')->setWidth(20);

            
                
                $sheet->setShowGridlines(false);
                $sheet->setCellValue('A2','PlanDue ID :');
                $sheet->setCellValue('B2',$this->data->plan_id);
                $sheet->setCellValue('A3','Created By :');
                $sheet->setCellValue('B3',$this->test2->name);
                $sheet->setCellValue('A4','Approved By :');
                $sheet->setCellValue('B4',$this->test2->name);
                $currentRow = 7;
                
                foreach($this->test as $t)
                {
                    $weight = Part::where('outpart',$t->outpart)->first();
                    $sheet->setCellValue("A{$currentRow}",$t->duedate);
                    $sheet->setCellValue("B{$currentRow}",$weight->type ?? null);
                    $sheet->setCellValue("C{$currentRow}",$t->outpart);
                    $sheet->setCellValue("D{$currentRow}",$weight->partname ?? null);
                    $sheet->setCellValue("E{$currentRow}",$t->quantity);
                    $sheet->setCellValue("F{$currentRow}",$t->issue);
                    $sheet->setCellValue("G{$currentRow}",'');
                    $sheet->setCellValue("H{$currentRow}",$t->quantity * $weight->weight);
                    $sheet->setCellValue("I{$currentRow}",'');
                    $sheet->setCellValue("J{$currentRow}",$t->body);

                    for ($col = 'A'; $col <= 'J'; $col++) {
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
            
        };
        $sheets[] = new class($this->data,$this->test,$this->test2) implements WithCustomStartCell ,WithStyles,WithEvents
        {
            protected $data;
            protected $test;
            protected $test2;

            public function __construct($data,$test,$test2)
            {
                $this->data = $data;
                $this->test = $test;
                $this->test2 = $test2;
            }

            public function startCell(): string
            {
                return "A1";
            }

            public function styles(Worksheet $sheet)
            {
            $count = 1; // Start with 1 if you want to start from row 1
            $sheet->getColumnDimension('A')->setWidth(12);


            // $sheet->setShowGridlines(false);    
            foreach ($this->test as $tt) {
                    $sheet->getStyle("A{$count}:A" . ($count + 9))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THICK);
                    $sheet->getStyle("A{$count}:H{$count}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
                    $sheet->getStyle("H{$count}:H" . ($count + 9))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THICK);
                    $sheet->getStyle("A".($count + 9).":H".($count+ 9))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
                    $sheet->getStyle("A{$count}:D{$count}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("E{$count}:E".($count + 2))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                    // $sheet->getStyle("E".($count + 2).":H".($count + 2))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("B".($count + 1).":B".($count + 9))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 1).":D".($count + 1))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 2).":H".($count + 2))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 3).":H".($count + 3))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 4).":C".($count + 4))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("C".($count + 4).":C".($count + 8))->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 5).":C".($count + 5))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A".($count + 8).":C".($count + 8))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);



                    $sheet->mergeCells("A{$count}:D{$count}");
                    $sheet->setCellValue("A{$count}", "THAI RUNG UNION CAR PUBLIC CO. TRU");
                    $sheet->setCellValue("A".($count +1),'P/O NO.');
                    $sheet->mergeCells("B".($count + 1).":D".($count + 1));
                    $sheet->setCellValue("B".($count + 1), $tt->issue);


                    $sheet->setCellValue("A".($count + 2),'Part Name');
                    $sheet->mergeCells("B".($count + 2).":D".($count + 2));
                    $partname = Part::where('outpart',$tt->outpart)->first();
                    $trupart = $partname->trupart ?? null;
                    $sheet->setCellValue("B".($count + 2), $partname->partname ?? null);

                    $sheet->setCellValue("A".($count + 3),'Customer');
                    $sheet->mergeCells("B".($count + 3).":H".($count + 3));
                    $sheet->setCellValue("B".($count + 3), $this->data->company_name);

                    $sheet->setCellValue("A".($count + 4),'Delivery');
                    $sheet->setCellValue("B".($count + 4),$tt->duedate);

                    $sheet->setCellValue("A".($count + 5),'Due');

                    $sheet->setCellValue("A".($count + 7),'QTY');
                    $sheet->mergeCells("B" . ($count + 6) . ":C" . ($count + 8));
                    $sheet->setCellValue("B".($count + 6),$tt->quantity);
                    $sheet->getStyle("B".($count + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B".($count + 6))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("B".($count + 6))->getFont()->setSize(16);
                
                    $sheet->mergeCells("E{$count}:H".($count+2));
                    $sheet->setCellValue("E".($count),"*{$trupart}*" );
                    $sheet->getStyle("E".($count))->getFont()->setName('IDAutomationHC39M Free Version')->setSize(9);
                    $sheet->getStyle("E".($count))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E".($count))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    $count += 13;   
                    
                }
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function(AfterSheet $event){
                        $event->sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                        $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

                        $event->sheet->getPageMargins()->setTop(0.5);
                        $event->sheet->getPageMargins()->setBottom(0.5);
                        $event->sheet->getPageMargins()->setLeft(0.5);
                        $event->sheet->getPageMargins()->setRight(0.5);
                    }
                ];
            }
        };
        $sheets[] = new class($this->data,$this->test,$this->test2,$this->sumvalue) implements WithHeadings,WithEvents,WithStyles,WithCustomStartCell
        {
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
                return ["ลำดับที่","ลูกค้า","เลขที่ของ","ชื่อของ","ปริมาณ","SNP","น้ำหนัก","น้ำหนักรวม","จำนวนหีบห่อ","ราคา/หน่วย","ราคารวม","REMARK"];
            }
            public function styles(Worksheet $sheet)
            {

                $sheet->mergeCells("B2:H3");
                $sheet->setCellValue("B2", $this->data->company_name);
                $sheet->getStyle("B2")->getFont()->setSize(20);
                $styleArray = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];
                $styleBottomArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];
                 
                $sheet->getStyle('A6:L6')->applyFromArray($styleArray);
                $sheet->getStyle('A7:L7')->applyFromArray($styleBottomArray);
                $sheet->getStyle('A6:A7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B6:B7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('C6:C7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('D6:D7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('E6:E7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('F6:F7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('G6:G7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('H6:H7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('I6:I7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('J6:J7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('K6:K7')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A7:L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension('6')->setRowHeight(20);
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(17);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(9);
                $sheet->getColumnDimension('F')->setWidth(9);
                $sheet->getColumnDimension('G')->setWidth(9);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(15);
                $sheet->getColumnDimension('L')->setWidth(15);

                $sheet->getStyle(6)->getFont()->setBold(true);
                $sheet->setCellValue("A7","(No.)");
                $sheet->setCellValue("B7","(Customer)");
                $sheet->setCellValue("C7","(Part no.)");
                $sheet->setCellValue("D7","(Part name)");
                $sheet->setCellValue("E7","(Q'ty)");
                $sheet->setCellValue("F7","(SNP)");
                $sheet->setCellValue("G7","(KG.)");
                $sheet->setCellValue("I7","(Pallet)");
                $sheet->setCellValue("J7","(Bath)");
                $sheet->setCellValue("K7","(Bath)");
                $currentrow = 8;

                foreach ($this->sumvalue as $index => $outpart){
                $type = Part::where("outpart",$outpart->outpart)->where("customer",$outpart->customer)->first();
                $sheet->setCellValue("A{$currentrow}",$index + 1);
                $sheet->setCellValue("B{$currentrow}",$type->type);
                $sheet->setCellValue("C{$currentrow}",$outpart->outpart);
                $sheet->setCellValue("D{$currentrow}",$type->partname);
                $sheet->setCellValue("E{$currentrow}",$outpart->total_quantity);
                $sheet->setCellValue("F{$currentrow}",$type->snp ?? null);
                $sheet->setCellValue("G{$currentrow}",$type->weight ?? null);
                $sheet->setCellValue("H{$currentrow}",$type->weight == null ? null :$outpart->total_quantity * $type->weight );
                $sheet->setCellValue("I{$currentrow}",ceil($outpart->total_quantity / $type->snp) ?? null);
                $sheet->setCellValue("J{$currentrow}", $outpart->total_price / $outpart->total_quantity ?? null);
                $sheet->setCellValue("K{$currentrow}", $outpart->total_price ?? null);

                for ($col = 'A'; $col <= 'L'; $col++) {
                    $sheet->getStyle("{$col}{$currentrow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$col}{$currentrow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("{$col}{$currentrow}")->getBorders()->getAllborders()->setBorderStyle(Border::BORDER_DASHED);
                }


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
        };

        

        return $sheets;
    }
}

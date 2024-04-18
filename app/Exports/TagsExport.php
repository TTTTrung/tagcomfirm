<?php

namespace App\Exports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Milon\Barcode\DNS1D;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Storage;

class TagsExport implements WithCustomStartCell, WithStyles ,WithEvents
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
            $sheet->setCellValue("A{$count}", 'KMT Production Parts Receiving Tag');
            $sheet->setCellValue("A".($count +1),'P/O NO.');
            $sheet->mergeCells("B".($count + 1).":D".($count + 1));
            $sheet->setCellValue("B".($count + 1), $tt->issue);


            $sheet->setCellValue("A".($count + 2),'Part Name');
            $sheet->mergeCells("B".($count + 2).":D".($count + 2));
            $partname = Part::where('outpart',$tt->outpart)->first();
            $sheet->setCellValue("B".($count + 2), $partname->partname);

            $sheet->setCellValue("A".($count + 3),'Vendor');
            $sheet->mergeCells("B".($count + 3).":E".($count + 3));
            $sheet->setCellValue("B".($count + 3), "THAI RUNG UNION CAR PUBLIC CO. TRU");

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
            $sheet->setCellValue("E".($count),"*{$partname->trupart}*");
            $sheet->getStyle("E".($count))->getFont()->setName('IDAutomationHC39M Free Version');
            $sheet->getStyle("E".($count))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".($count))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $count += 13;   
            
        }
    }

    // public function drawings()
    // {
    //     $left = true;
    //     $count = 1;
    //     foreach ($this->test as $tt) {
    //     if($left){
    //         $barcode = new DNS1D();
    //         $trupart=Part::where('outpart',$tt->outpart)->first();
    //         $barcodeImage = $barcode->getBarcodePNG($trupart->trupart,'C128');

    //         $tempBarcodePath = tempnam(sys_get_temp_dir(), 'barcode_');
    //         file_put_contents($tempBarcodePath, $barcodeImage);

    //         $drawing = new Drawing();
    //         $drawing->setName('Barcode');
    //         $drawing->setDescription('Barcode');
    //         $drawing->setPath($tempBarcodePath); // Set the path to the temporary barcode image file
    //         $drawing->setCoordinates("A" . ($count + 9));
    //         $drawing->setHeight(50);
    //         $drawing->setWidth(200);

    //         // Add the drawing to the worksheet
    //         // $sheet->addDrawing($drawing);

    //         // Remove the temporary barcode image file
    //         unlink($tempBarcodePath);
    //     }
    //     else{
    //         $barcode = new DNS1D();
    //         $barcode->setStorPath(__DIR__ . '/cache/');
    //         $trupart=Part::where('outpart',$tt->outpart)->first();
    //         $barcodeImage = $barcode->getBarcodePNG($trupart->trupart,'C128');

    //         $drawing = new Drawing();
    //         $drawing->setname('Barcode');
    //         $drawing->setDescription('Barcode');
    //         $drawing->setPath($barcodeImage);
    //         $drawing->setCoordinates("J".($count + 9));


    //     }
    //     }
    // }
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
}

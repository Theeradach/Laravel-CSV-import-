<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
// Formatting the fonts and sizes
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell
{

    public function collection()
    {
        return DB::table('users')
            ->limit(10)
            ->get();
    }

    /**
     * @var $users
     */
    public function map($users): array
    {
        return [
            $users->email,
            $users->name,
        ];
    }

    public function headings(): array
    {
        return [
            'email',
            'name',
        ];
    }

    public function startCell(): string
    {
        return 'B4';
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class    => function (BeforeExport $event) { },
            BeforeWriting::class    => function (BeforeWriting $event) { },
            BeforeSheet::class    => function (BeforeSheet $event) {
                $arrayData = [
                    ['10:30-11:00'], ['11:00-11:30'], ['11:30-12:00'], ['12:00-12:30']
                ];
                $event->sheet->getDelegate()->fromArray($arrayData, NULL, 'A4');

                $rowArray2 = ['Value1', 'Value2', 'Value3', 'Value4'];
                $columnArray = array_chunk($rowArray2, 1);
                $event->sheet->getDelegate()->fromArray(
                    $columnArray,   // The data to set
                    NULL,           // Array values with this value will not be set
                    'D13'            // Top left coordinate of the worksheet range where
                );
            },

            AfterSheet::class    => function (AfterSheet $event) {
                // Set Styling 
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                for ($i = 4; $i < 8; $i++) {
                    // Retrieving a cell value by coordinate
                    $event->sheet->getDelegate()->getCellByColumnAndRow($i, 4)->setValue('Jackky');
                }
                // Acess each cell and set value
                $event->sheet->getDelegate()->setCellValue('E9', 1513789642);
                // value from array 
                $arrayData = [
                    [NULL, 2010, 2011, 2012],
                    ['Q1',   12,   15,   21],
                    ['Q2',   56,   73,   86],
                    ['Q3',   52,   61,   69],
                    ['Q4',   30,   32,    0],
                ];
                $event->sheet->getDelegate()->fromArray($arrayData, NULL, 'H1');

                // Retrieving a range of cell values to an array
                $dataArray = $event->sheet->getDelegate()
                    ->rangeToArray(
                        'A2:B5',     // The worksheet range that we want to retrieve
                        NULL,        // Value that should be returned for empty cells
                        TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                        TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                        TRUE         // Should the array be indexed by cell row and cell column
                    );
                // dd($dataArray);

                // Setting formula 
                $event->sheet->getDelegate()->setCellValue(
                    'H10',
                    '=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))'
                );

                $rowArray = ['Value1', 'Value2', 'Value3', 'Value4'];
                $event->sheet->getDelegate()->fromArray(
                    $rowArray,   // The data to set
                    NULL,        // Array values with this value will not be set
                    'A1'         // Top left coordinate of the worksheet range where
                    //    we want to set these values (default is A1)
                );
            },
        ];
    }
}

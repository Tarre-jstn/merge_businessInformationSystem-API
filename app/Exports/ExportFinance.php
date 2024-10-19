<?php

namespace App\Exports;

use App\Models\Finance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;
use Carbon\carbon;

class ExportFinance implements FromCollection, WithHeadings, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $selectedCategories;

    public function __construct($startDate, $endDate, $selectedCategories)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->selectedCategories = $selectedCategories;
    }

    // Fetch only the necessary columns and group by category
    public function collection()
    {
        $finances = Finance::whereBetween('date', [$this->startDate, $this->endDate])
            ->whereIn('category', $this->selectedCategories)
            ->orderBy('category')
            ->orderBy('date')
            ->get(['date', 'description', 'category', 'amount']);
    
        $groupedFinances = $finances->groupBy('category');
    
        $output = new Collection();
    
        foreach ($groupedFinances as $category => $financeItems) {
            $output->push([
                'date' => 'Date',
                'description' => 'Description',
                'category' => 'Category',
                'amount' => 'Amount',
            ]);
    
            foreach ($financeItems as $finance) {
                $output->push([
                    // Manually convert to Carbon if it's a string
                    'date' => Carbon::parse($finance->date)->format('Y-m-d'),
                    'description' => $finance->description,
                    'category' => $finance->category,
                    'amount' => $finance->amount,
                ]);
            }
            $output->push(['', '', '', '']);
        }
        
    
        return $output;
    }

    // Since headings are now included in the data, we don't need the separate headings() method
    public function headings(): array
    {
        return [];
    }

    // Apply styling to the worksheet
    public function styles(Worksheet $sheet)
    {
        // Apply general styling for header rows (bold, centered, with background color)
        $highestRow = $sheet->getHighestRow();
        
        // Center align all cells (both horizontally and vertically)
        $sheet->getStyle('A1:D' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    
        // Apply special styling for header rows
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($sheet->getCell('A' . $row)->getValue() === 'Date') {
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF000000'], // Black background for header
                    ],
                ]);
            }
        }
    
        // Auto-size columns for better readability
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Apply alternating row colors for data rows
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($sheet->getCell('A' . $row)->getValue() !== 'Date') { // Skip header rows for alternating colors
                $color = ($row % 2 == 0) ? 'FFEFEFEF' : 'FFFFFFFF'; // Light grey for even rows, white for odd rows
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $color],
                    ],
                ]);
            }
        }
    
        return [];
    }
}
<?php

namespace App\Exports;

use App\InvoiceSummaryByDateXlsx;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // Import WithStyles
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Import Worksheet
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Import Alignment
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportInvoice implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'Invoice ID',
            'Date',
            'Terms',
            'Payment Type',
            'Status',
            'Authorized Representative',

            'Customer Name',
            'Address',
            'TIN',
            'Business Style',
            'P.O. Number',
            'OSCA/PWD ID No.',
            
            'All Items',
            'All Additionals',
            'Computations',
            'Other Computations',
        ];
    }

    public function map($invoice): array
    {
        $itemsData = $invoice->items->map(function ($item) {
            return '• ' . $item->product_name . ' -> (Price: ' . $item->product_price . 
                ', Qty:: ' . $item->quantity . 
                ', Total Price: ' . $item->final_price . 
                ')';
        })->implode("\n");  // Newline-separated items

        $additionalsData = $invoice->additionals->map(function ($additional) {
            return '• ' . $additional->addtl_Costs_Description . ' -> (Price: ' . $additional->aCD_amount . 
                ', Qty: ' . $additional->aCD_quantity . 
                ', Total Price: ' . $additional->aCD_Total_Amount . 
                ')';
        })->implode("\n");  // Newline-separated items

        
        // Concatenate all computation fields into a single string, using a newline "\n" as the separator
        $computationData = $invoice->computation ? implode("\n", [
            'Total Amount Due: ' . $invoice->computation->total_Amount_Due,
            'Tax: ' . $invoice->computation->tax,
            'VAT Inclusive: ' . $invoice->computation->VAT_Inclusive,
            'Senior/PWD Discountable: ' . $invoice->computation->senior_PWD_Discountable,
            'Less SC/PWD Discount: ' . $invoice->computation->Less_SC_PWD_Discount,
            'Less SC/PWD Discount Percentage: ' . $invoice->computation->Less_SC_PWD_Discount_Percent,
            'VATable Sales: ' . $invoice->computation->VATable_Sales,
            'VAT Exempt Sales: ' . $invoice->computation->VAT_Exempt_Sales,

        ]) : 'N/A';
        $otherComputationData = $invoice->computation ? implode("\n", [
            'Zero Rated Sales: ' . $invoice->computation->Zero_Rated_Sales,
            'VAT Amount: ' . $invoice->computation->VAT_Amount,
            'Less VAT: ' . $invoice->computation->Less_VAT,
            'Amount NET of VAT: ' . $invoice->computation->Amount_NET_of_VAT,
            'Amount Due: ' . $invoice->computation->Amount_Due,
            'Add VAT: ' . $invoice->computation->Add_VAT,
        ]) : 'N/A';
    
        return [
            $invoice->invoice_id,
            $invoice->date,
            $invoice->terms ?? 'N/A',
            $invoice->payment_Type,
            $invoice->status,
            $invoice->authorized_Representative,
            $invoice->customer_Name,
            $invoice->customer_Address,
            $invoice->customer_TIN,
            $invoice->customer_Business_Style,
            $invoice->customer_PO_No,
            $invoice->customer_OSCA_PWD_ID_No,
            // Store all computation fields in one cell with newlines
            $itemsData,
            $additionalsData,
            $computationData,
            $otherComputationData,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply a general style for header row (bold, centered, with background color)
        $sheet->getStyle('A1:Q1')->applyFromArray([
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
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF000000'], // Black background
            ],
        ]);
    
        // Set text alignment for general data cells
        $sheet->getStyle('A2:Q' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    
        // Enable text wrapping for the 'items' column (adjust 'M' as needed)
        $sheet->getStyle('M2:M' . $sheet->getHighestRow())
              ->getAlignment()
              ->setWrapText(true);
    
        // Enable text wrapping for the 'computations' column (adjust 'N' as needed)
        $sheet->getStyle('N2:N' . $sheet->getHighestRow())
              ->getAlignment()
              ->setWrapText(true);
    
        // Optional: Auto-size columns for better readability
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(false);
            // Set column width manually (adjust as necessary)
            $sheet->getColumnDimension($column)->setWidth(20); 
        }

        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $color = ($row % 2 == 0) ? 'FFEFEFEF' : 'FFFFFFFF'; // Light grey for even rows, white for odd rows
    
            $sheet->getStyle('A' . $row . ':Q' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $color], // Apply alternating row colors
                ],
            ]);
        }
    
        return [];
    }
}
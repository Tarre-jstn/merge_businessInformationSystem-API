<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProduct implements FromQuery, WithHeadings, WithStyles, WithMapping
{
    /**
     * Return the query of products.
     */
    public function query()
    {
        return Product::query();
        
    }

    /**
     * Map the data to be exported.
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->brand,
            $product->price,
            $product->category,
            $product->stock,
            $product->sold ?? 0,  // This will now always be 0 or a positive integer
            $product->status,
            $product->expDate,
            $product->image,
            $product->featured,
            $product->on_sale,
            $product->on_sale_price,
            $product->seniorPWD_discountable,
            $product->business_id,
            $product->created_at,
            $product->updated_at,
        ];
    }

    /**
     * Define headings for all columns.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Brand',
            'Price',
            'Category',
            'Stock',
            'Sold',


            'Status',
            'Expiration Date',
            'Image',

            'Featured',
            'On Sale',
            'On Sale Price',
            'Senior/PWD Discountable',
            
            
            'Business ID',
            'Created At',
            'Updated At',


        ];
    }

    /**
     * Apply styles to the sheet, especially for the header row.
     */
    public function styles(Worksheet $sheet)
    {
        // Apply styles to the first row (header)
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF000000'], // Black background
            ],
        ]);

        return [
            // Apply this style only to the first row (header)
            1 => ['font' => ['bold' => true]],
        ];
    }
}
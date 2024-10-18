<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule; 
use Carbon\Carbon;

class ImportProduct implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Define the logic to import each row into the Product model.
     */
    public function model(array $row)
    {
        return new Product([
            'id'                     => $row['id'],
            'name'                   => $row['name'],
            'description'            => $row['description'],
            'brand'                  => $row['brand'], // No need for formatBrand here
            'price'                  => $row['price'],
            'category'               => $row['category'],
            'stock'                  => $row['stock'],
            'sold'                   => $row['sold'] ?? 0,
            'status'                 => $row['status'],
            'expDate'                => $this->transformDate($row['expiration_date']),
            'image'                  => $row['image'],
            'featured'               => $row['featured'],
            'on_sale'                => $row['on_sale'],
            'on_sale_price'          => $row['on_sale_price'],
            'seniorPWD_discountable' => $row['seniorpwd_discountable'],
            'business_id'            => $row['business_id'],
            'created_at'             => $this->transformDate($row['created_at']),
            'updated_at'             => $this->transformDate($row['updated_at']),
        ]);
    }

    /**
     * Transform a date value into a Carbon object.
     */
    private function transformDate($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * Prepare data for validation, converting the brand to a string before validation.
     */
    public function prepareForValidation($data)
    {
        // Convert brand, description, and category to strings and trim any whitespace
        $data['brand'] = trim((string)$data['brand']);
        $data['description'] = trim((string)$data['description']);
        $data['category'] = trim((string)$data['category']);
        
        return $data;
    }

    /**
     * Define validation rules.
     */
    public function rules(): array
    {
        return [
            '*.id'                     => 'required|integer',
            '*.name'                   => 'required|string',
            '*.description'            => 'required|string',
            '*.brand'                  => 'required|string',
            '*.price'                  => 'required|numeric',
            '*.category'               => 'required|string',
            '*.stock'                  => 'required|integer',
            '*.sold'                   => 'nullable|integer',
            '*.status'                 => 'required|string',
            '*.expiration_date'        => 'required|date',
            '*.image'                  => 'nullable|string',
            '*.featured'               => 'required|in:true,false',
            '*.on_sale'                => 'required|in:yes,no',
            '*.on_sale_price'          => 'nullable|numeric',
            '*.seniorpwd_discountable' => 'required|in:yes,no',
            '*.business_id'            => 'required|integer',
            '*.created_at'             => 'nullable|date',
            '*.updated_at'             => 'nullable|date',
        ];
    }

    /**
     * Provide custom messages for validation errors.
     */
    public function customValidationMessages()
    {
        return [
            '*.featured.in'               => 'The featured field must be either "true" or "false".',
            '*.on_sale.in'                => 'The on sale field must be either "yes" or "no".',
            '*.seniorpwd_discountable.in' => 'The Senior/PWD Discountable field must be either "yes" or "no".',
        ];
    }
}

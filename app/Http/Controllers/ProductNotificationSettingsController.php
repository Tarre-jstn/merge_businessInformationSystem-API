<?php

namespace App\Http\Controllers;

use App\Exports\ExportProduct;
use App\Imports\ImportProduct;
use App\Models\ProductNotificationSettings;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Validators\ValidationException; 




class ProductNotificationSettingsController extends Controller
{
    public function show()
    {
        return ProductNotificationSettings::all();
    }



    public function updateCounts(Request $request)
    {
        foreach ($request->all() as $setting) {
            ProductNotificationSettings::where('stock_expDate', $setting['stock_expDate'])
                ->update(['count' => $setting['count']]);
        }
        return response()->json(['message' => 'Settings updated successfully']);
    }

}

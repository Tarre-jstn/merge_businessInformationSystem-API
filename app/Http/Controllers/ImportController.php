<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    /**
     * Handle the SQL file import.
     */
    public function import(Request $request)
    {
        // Validate that a file is uploaded and it must be an SQL file
        $request->validate([
            'sql_file' => 'required|file|mimetypes:text/plain,application/octet-stream,application/sql',
        ]);
    
        // Get the uploaded file
        $file = $request->file('sql_file');
    
        // Read the contents of the file
        $sqlContent = file_get_contents($file->getRealPath());
    
        // Split SQL queries into individual queries
        $queries = explode(';', $sqlContent);
    
        // Execute each query
        foreach ($queries as $query) {
            $query = trim($query);  // Remove any leading/trailing whitespace
            if (!empty($query)) {
                DB::statement($query);  // Execute the query
            }
        }
    
        return response()->json(['message' => 'Database imported successfully!']);
    }    
}

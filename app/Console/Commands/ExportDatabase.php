<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExportDatabase extends Command
{
    protected $signature = 'db:export';
    protected $description = 'Export specific tables with INSERT INTO statements only';

    public function handle()
    {
        $tables = ['categories', 'financial_report', 'invoice', 'products', 'websites'];

        $backupPath = storage_path('app/backups/backup.sql');

        $sqlContent = '';

        foreach ($tables as $table) {
            $rows = DB::table($table)->get();
            
            if ($rows->isEmpty()) {
                continue; // Skip if the table is empty
            }

            // Loop through the rows and generate INSERT statements
            foreach ($rows as $row) {
                $rowArray = (array) $row; // Convert row to an array
                $columns = implode(", ", array_keys($rowArray)); // Get column names
                $values = implode("', '", array_map('addslashes', array_values($rowArray))); // Escape and format values

                $sqlContent .= "REPLACE INTO `$table` ($columns) VALUES ('$values');\n";
            }
            $sqlContent .= "\n";
        }

        // Save the SQL content to a file
        Storage::disk('local')->put('backups/backup.sql', $sqlContent);

        $this->info('Database backup completed and saved to: ' . $backupPath);
    }
}

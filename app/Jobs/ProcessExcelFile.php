<?php

namespace App\Jobs;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Log;


class ProcessExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $storedFilePath;
    public function __construct($storedFilePath)
    {
        //
        $this->storedFilePath = $storedFilePath;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Retrieve the file from the local disk
            $fileContents = Storage::disk('public')->url($this->storedFilePath);
          
      
         
            $data = Excel::toArray([], $fileContents);
            $rowsToInsert = [];
            $rowsToUpdate = [];
  
            for ($i = 1; $i < count($data[0]); $i++) {
                $row = $data[0][$i];
                if (array_filter($row)) {
                    $rowData = [
                        'perso_id' => $row[0],
                        'email' => $row[1],
                        'description' => $row[2],
                        // Add more columns as needed
                    ];
                    $rowData['slug'] = 'dos1';
                    $rowData['created_at'] = now();

                    // Check if a record with the same 'perso_id' exists
                    $existingRow = Dossier::where('perso_id', $rowData['perso_id'])->first();

                    if ($existingRow) {
                        // If a matching record exists, update it
                        $rowsToUpdate[] = $rowData;
                    } else {
                        // If no matching record exists, insert a new record
                        $rowsToInsert[] = $rowData;
                    }
                }
            }

// Batch insert new rows
            Dossier::insert($rowsToInsert);

// Batch update existing rows
            foreach ($rowsToUpdate as $row) {
                Dossier::where('perso_id', $row['perso_id'])->update($row);
            }

        } catch (\Exception $e) {
            // Handle exceptions, e.g., log the error
        }
    }
}

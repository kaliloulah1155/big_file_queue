<?php

namespace App\Jobs;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $donnees;

    public function __construct($donnees)
    {
        $this->donnees = $donnees;
    }

    public function handle(): void
    {
        try {
            $rowsToInsert = [];
            $rowsToUpdate = [];

            if (is_array($this->donnees) && count($this->donnees) > 0) {
                // Skip the first row (header row)
                $data = $this->donnees;
                array_shift($data); // Remove the first row

                foreach ($data as $row) {
                    $rowData = [
                        'perso_id' => $row[0],
                        'email' => $row[1],
                        'description' => $row[2],
                    ];

                    // Your additional logic here, e.g., setting 'slug' and 'created_at'
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

                // Batch insert new rows
                Dossier::insert($rowsToInsert);

                // Batch update existing rows
                foreach ($rowsToUpdate as $row) {
                    Dossier::where('perso_id', $row['perso_id'])->update($row);
                }
            }

        } catch (\Exception $e) {
            // Handle exceptions, e.g., log the error
        }
    }
}

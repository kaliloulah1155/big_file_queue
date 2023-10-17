<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadControler extends Controller
{

    public function upload(Request $request)
    {
        try {
            $file = $request->file('file');

            if ($file) {
                // Authenticate the user
                Auth::loginUsingId(1);

                $user = auth()->user();

                // Clear the media collection (if needed)
                $user->clearMediaCollection('excel-files');

                // Add the file to the media collection
                $media = $user->addMedia($file)->toMediaCollection('excel-files');

                if ($media) {
                    // Read the Excel file using the Media Library
                    $mediaFilePath = $media->getPath();
                    $spreadsheet = IOFactory::load($mediaFilePath);

                    // Select a specific worksheet if needed
                    $worksheet = $spreadsheet->getActiveSheet();

                    // Get the data from the worksheet
                    $data = $worksheet->toArray();

                    // Now you have the Excel data in the $data array
                    dd($data);
                }

                // Dispatch the job with the stored file path
                // ProcessExcelFile::dispatch($storedFilePath);

                return "Processing of the Excel file has been queued.";

                return "File uploaded and stored in the 'excel-files' collection.";
            } else {
                return "No file uploaded.";
            }
        } catch (\Exception $e) {
            // Handle the exception, e.g., log the error and provide a user-friendly error message.
            return "An error occurred: " . $e->getMessage();
        }
    }

}

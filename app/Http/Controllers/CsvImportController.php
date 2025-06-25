<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lab;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // To log errors

class CsvImportController extends Controller
{
    /**
     * Show the CSV import form.
     */
    public function show()
    {
        $labs = Lab::all();
        return view('import.show', compact('labs'));
    }

    /**
     * Handle the CSV file upload and process the data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'lab_id' => 'required|exists:labs,id',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, "r");

        $header = fgetcsv($file);
        
        // --- IMPROVEMENT: Clean up all header names robustly ---
        $cleanedHeaders = [];
        foreach ($header as $key => $value) {
            // Remove BOM, trim whitespace from start/end
            $cleanedValue = trim(preg_replace('/^\x{FEFF}/u', '', $value));
            $cleanedHeaders[] = $cleanedValue;
        }
        $header = $cleanedHeaders;
        // --- END IMPROVEMENT ---

        DB::beginTransaction();
        try {
            $rowCount = 1; 
            while (($row = fgetcsv($file)) !== false) {
                $rowCount++;
                
                // Prevent error if a row has a different number of columns than the header
                if (count($header) !== count($row)) {
                    continue; 
                }

                $data = array_combine($header, $row);

                if (empty(trim($data['PC#'] ?? ''))) {
                    continue;
                }
                
                $equipment = Equipment::create([
                    'tag_number' => trim($data['PC#']),
                    'lab_id' => $request->lab_id,
                    'status' => trim($data['Status']), // Changed 'Status ' to 'Status' after trimming
                    'notes' => null,
                ]);

                $componentColumns = [
                    'Monitor' => 'Monitor',
                    'OS' => 'OS',
                    'Processor' => 'Processor',
                    'Motherboard' => 'Motherboard',
                    'Memory' => 'Memory',
                    'Storage' => 'Storage',
                    'Video Card' => 'Video Card',
                    'PSU' => 'PSU',
                ];

                foreach($componentColumns as $columnName => $componentType) {
                    if (!empty(trim($data[$columnName] ?? ''))) {
                        $equipment->components()->create([
                            'type' => $componentType,
                            'description' => trim($data[$columnName]),
                            'serial_number' => match($columnName) {
                                'Monitor' => trim($data['Monitor Serial Num'] ?? null),
                                'Processor' => trim($data['CPU Serial Num'] ?? null),
                                default => null
                            },
                        ]);
                    }
                }
            }

            fclose($file);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            
            Log::error('CSV Import Failed: ' . $e->getMessage() . ' at row ' . $rowCount . ' with data: ' . implode(', ', $row ?? []));

            return back()->withErrors(['csv_file' => 'An error occurred during import at row ' . $rowCount . '. Please check your CSV data. Details: ' . $e->getMessage()]);
        }

        return redirect()->route('equipment.index')->with('success', 'CSV imported successfully!');
    }
}
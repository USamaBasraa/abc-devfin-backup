<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Log;

class StudentSecurityDeposits implements ToModel, WithHeadingRow, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    public function batchSize(): int
    {
        return 100; // Insert 100 rows at a time
    }

    public function chunkSize(): int
    {
        return 100; // Read 100 rows at a time
    }

    public function model(array $row)
    {
        try {

            $studentId = $row['std_id'] ?? null;

            // Skip if empty
            if ($studentId === null) {
                return null;
            }

            // ❗ Check if already imported using SSD_STUDENT_ID
            $alreadyExists = DB::table('STUDENT_SECURITY_DEPOSITS')
                ->where('SSD_STUDENT_ID', $studentId)
                ->exists();

            if ($alreadyExists) {
                // Do NOT import again
                return null;
            }

            // Insert new row
            DB::table('STUDENT_SECURITY_DEPOSITS')->insert([
                'SSD_STUDENT_ID'     => $studentId,
                'SSD_ENROLLMENT_NO'  => $row['enrollment'] ?? null,
                'SSD_STUDENT_NAME'   => $row['student_name'] ?? null,
                'SSD_SECURITY_AMOUNT'=> is_numeric($row['security']) ? $row['security'] : null,
                'SSD_DESC'           => $row['status'] ?? null,
                'SSD_ENTRY_USER'     => Auth::user()->username ?? 'system',
                'SSD_CREATED_AT'     => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('SSD Row Import Failed', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
        }

        return null; // Important for ToModel when manually inserting
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('SSD Row Validation Failed', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }
}

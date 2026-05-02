<?php

namespace App\Imports;

use App\Models\MslDoctor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DoctorImport implements ToCollection, WithChunkReading
{
    public $inserted = 0;
    public $updated = 0;

    public function collection(Collection $rows)
    {
        $insertData = [];
        $updateData = [];

        // Collect all msl_codes in this chunk (skip header row)
        $mslCodes = $rows->slice(1)->pluck(3)->filter()->unique()->values()->toArray();

        // ONE query to get all existing records in this chunk
        $existingMap = MslDoctor::whereIn('msl_code', $mslCodes)
            ->pluck('msl_code')
            ->flip()
            ->toArray(); // ['MSL001' => 0, 'MSL002' => 1, ...]

        foreach ($rows as $key => $row) {
            if ($key == 0) continue;

            $positionCode = $row[0];
            $name         = $row[1];
            $specility    = $row[2];
            $mslCode      = $row[3];
            $city         = $row[4];

            if (!$mslCode) continue;

            $record = [
                'msl_code'      => $mslCode,
                'name'          => $name,
                'degree'        => $specility,
                'city'          => $city,
                'employee_code' => $positionCode,
                'updated_at'    => now(),
            ];

            if (isset($existingMap[$mslCode])) {
                $updateData[] = $record;
                $this->updated++;
            } else {
                $record['created_at'] = now();
                $insertData[] = $record;
                $this->inserted++;
            }
        }

        // Bulk insert new records
        if (!empty($insertData)) {
            MslDoctor::insert($insertData);
        }

        // Bulk upsert existing records (updates only matched ones)
        if (!empty($updateData)) {
            MslDoctor::upsert(
                $updateData,
                ['msl_code'],           // unique key to match on
                ['name', 'degree', 'city', 'employee_code', 'updated_at'] // columns to update
            );
        }
    }

    public function chunkSize(): int
    {
        return 500; // Safer chunk size for upsert
    }
}

<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;

class EmployeeImport implements ToCollection
{
    public $inserted = 0;
    public $updated = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {

            // Skip heading row
            if ($key == 0) {
                continue;
            }

            $name           = $row[0];
            $empCode        = $row[1];
            $positionCode   = $row[2];
            $designation    = $row[3];
            $hqName         = $row[4];

            if (!$empCode) {
                continue;
            }

            $user = User::where('emp_code', $empCode)->first();

            if ($user) {
                // UPDATE
                $user->update([
                    'name'          => $name,
                    'position_code' => $positionCode,
                    'designation'   => $designation,
                    'hq_name'       => $hqName,
                ]);

                $this->updated++;

            } else {
                // INSERT
                User::create([
                    'name'          => $name,
                    'emp_code'      => $empCode,
                    'position_code' => $positionCode,
                    'designation'   => $designation,
                    'hq_name'       => $hqName,
                ]);

                $this->inserted++;
            }
        }
    }
}

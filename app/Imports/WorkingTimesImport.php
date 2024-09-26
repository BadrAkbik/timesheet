<?php

namespace App\Imports;

use App\Models\WorkingTime;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class WorkingTimesImport implements ToModel, WithValidation, SkipsOnFailure
{

    use SkipsFailures;
    protected $site_id;

    public function __construct($site_id)
    {
        $this->site_id = $site_id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new WorkingTime([
            'guard_number' => $row[0],
            'site_id' => $this->site_id,
            'date' => Date::excelToDateTimeObject($row[1]),
            'time' => Date::excelToDateTimeObject($row[2]),
            'period' => $row[3],
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'numeric'],
            '1' => ['required', 'numeric'],
            '2' => ['required'],
            '3' => ['required', 'string'],
        ];
    }
}

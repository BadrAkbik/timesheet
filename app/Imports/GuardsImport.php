<?php

namespace App\Imports;

use App\Models\Guard;
use App\Models\JobTitle;
use App\Models\Site;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class GuardsImport implements ToModel, WithValidation
{
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Guard([
            'guard_number' => $row[0],
            'name' => $row[1],
            'id_number' => $row[2],
            'site_id' => Site::firstWhere('name', $row[3])->id,
            'job_title_id' => JobTitle::firstWhere('name', $row[4])->id,
            'start_date' => Date::excelToDateTimeObject($row[5]),
            'phone' => $row[6],
            'iban' => $row[7],
            'bank' => $row[8],
            'salary' => $row[9],
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'numeric'],
            '1' => ['required', 'string'],
            '2' => ['required', 'numeric', Rule::unique('guards', 'id_number')],
            '3' => ['required', 'exists:sites,name'],
            '4' => ['required', 'exists:job_titles,name'],
            '5' => ['nullable', 'numeric'],
            '6' => ['nullable', 'string'],
            '7' => ['nullable', 'string'],
            '8' => ['nullable', 'string'],
            '9' => ['nullable', 'string'],
        ];
    }
}

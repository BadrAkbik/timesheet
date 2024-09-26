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
            'guard_number' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'id_number' => ['required', 'numeric', Rule::unique('guards', 'id_number')],
            'site' => ['required', 'exists:sites,name'],
            'job' => ['required', 'exists:job_titles,name'],
            'start_date' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string'],
            'ibane' => ['nullable', 'string'],
            'bank' => ['nullable', 'string'],
            'salary' => ['nullable', 'string'],
        ];
    }
}

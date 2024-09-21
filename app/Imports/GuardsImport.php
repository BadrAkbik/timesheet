<?php

namespace App\Imports;

use App\Models\Guard;
use App\Models\JobTitle;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithValidation;


class GuardsImport implements ToModel, WithValidation, WithHeadingRow
{
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Guard([
            'guard_number' => $row['guard_number'],
            'name' => $row['name'],
            'id_number' => $row['id_number'],
            'site_id' => Site::firstWhere('name', $row['site'])->id,
            'job_title_id' => JobTitle::firstWhere('name', $row['job'])->id,
            'start_date' => $this->formatDate($row['start_date']),
            'phone' => $row['phone'],
        ]);
    }
    private function formatDate($date)
    {
        try {
            return $date ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null; // or handle the error as needed
        }
    }

    public function rules(): array
    {
        return [
            'guard_number' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'id_number' => ['required', 'numeric', Rule::unique('guards', 'id_number')],
            'site' => ['required', 'exists:sites,name'],
            'job' => ['required', 'exists:job_titles,name'],
            'start_date' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
        ];
    }
}

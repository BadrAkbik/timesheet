<?php

namespace App\Imports;

use App\Models\Guard;
use App\Models\WorkingTime;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Closure;

class WorkingTimesImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnFailure
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
            'guard_id' => Guard::where('site_id', $this->site_id)->Where('guard_number', $row['guard_number'])->first()?->id,
            'date' => $this->formatDate($row['date']),
            'time' => $row['time'],
            'period' => $row['period'],
        ]);
    }

    private function formatDate($date)
    {
        try {
            return $date ? Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null; // or handle the error as needed
        }
    }

    public function rules(): array
    {

        return [
            'guard_number' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $guard_id = Guard::where('site_id', $this->site_id)->Where('guard_number', $value)->first()?->id;
                    if (!isset($guard_id)) {
                        $fail("لا يوجد حارس مطابق لقيمة {$attribute} في هذا الموقع");
                    }
                }
            ],
            'date' => ['required', 'string'],
            'time' => ['required', 'string'],
            'period' => ['required', 'string'],
        ];
    }
}

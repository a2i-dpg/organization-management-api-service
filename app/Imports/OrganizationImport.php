<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OrganizationImport implements ToCollection, WithValidation, WithHeadingRow
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => function($attribute, $value, $onFailure) {
                Log::info("Name ********************** 11111111");
                Log::info($attribute);
                Log::info($value);
            },
            'contact_person_name' => function($attribute, $value, $onFailure) {
                Log::info("contact ********************** 2222222222222");
                Log::info($attribute);
                Log::info($value);
            },
            'mobile' => Rule::in(['1674248402'])
        ];
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Log::info("Value >>>>>> Value <<<<<<<<<<");
            Log::info(json_encode($row));
        }
    }
}

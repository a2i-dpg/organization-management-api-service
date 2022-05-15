<?php

namespace App\Imports;

use App\Models\FourIRInitiative;
use App\Models\FourIROccupation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class FourIrInitiativesImport implements ToCollection, SkipsEmptyRows, WithValidation, WithHeadingRow
{
    /**
     * @param $data
     * @param $index
     * @return mixed
     */
    public function prepareForValidation($data, $index): mixed
    {
        if (!empty($data['start_date'])) {
            $data['start_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $data['start_date'])->format('Y-m-d');
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $data['end_date'])->format('Y-m-d');
        }

        if (!empty($data['task'])) {
            $explode = explode('|', $data['task']);
            if (sizeof($explode) == 2 && !empty($explode[0])) {
                $explodedValue = trim($explode[0]);
                if (is_numeric($explodedValue)) {
                    $explodedValue = (int)$explodedValue;
                }
                $data['task'] = array($explodedValue);
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Store organizations as bulk.
     * Don't remove this collection method
     *
     * @param Collection $collection
     * @return void
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {

    }
}

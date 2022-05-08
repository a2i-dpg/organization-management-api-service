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
        if (!empty($data['task'])) {
            $taskId = FourIRInitiative::TASKS[$data['task']];
            $data['task'] = array($taskId);
        }

        if(!empty($data['four_ir_occupation_id'])){
            $fourIrOccupation = FourIROccupation::where('title', $data['four_ir_occupation_id'])->first();
            if(!empty($fourIrOccupation)){
                $data['four_ir_occupation_id'] = $fourIrOccupation->id;
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

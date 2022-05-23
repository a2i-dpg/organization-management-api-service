<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRInitiative;

/**
 * Class FourIRFileLogService
 * @package App\Services
 */
class FourIRFileLogService
{
    /**
     * @param array $data
     * @param string $step
     * @return void
     */
    public function storeFileLog(array $data, string $step): void
    {
        if (!empty($data['file_path'])) {
            $this->store($data, $step);
        }
    }

    /**
     * @param string|null $filePath
     * @param array $data
     * @param string $step
     * @return void
     */
    public function updateFileLog(string|null $filePath, array $data, string $step): void
    {
        if (!empty($data['file_path']) && $filePath != $data['file_path']) {
            $this->store($data, $step);
        }
    }

    /**
     * @param array $data
     * @param string $step
     * @return void
     */
    private function store(array $data, string $step)
    {
        $fourIrFileLog = new FourIRFileLog();
        $fourIrFileLog->fill([
            'four_ir_initiative_id' => $data['four_ir_initiative_id'],
            'file_path' => $data['file_path'],
            'module_type' => $step,
            'accessor_type' => $data['accessor_type'],
            'accessor_id' => $data['accessor_id'],
            'row_status' => $data['row_status'] ?? BaseModel::ROW_STATUS_ACTIVE
        ]);
        $fourIrFileLog->save();
    }

    /**
     * If you want to store file only when a new file path is given for a module of a project
     *
     * @param array $data
     * @param string $step
     * @return void
     */
    public function storeOrUpdateFileLog(array $data, string $step): void
    {
        FourIRFileLog::updateOrCreate(
            [
                'four_ir_initiative_id' => $data['four_ir_initiative_id'],
                'file_path' => $data['file_path'],
                'module_type' => $step
            ],
            [
                'accessor_type' => $data['accessor_type'],
                'accessor_id' => $data['accessor_id'],
                'row_status' => $data['row_status'] ?? BaseModel::ROW_STATUS_ACTIVE
            ]
        );
    }


    /**
     * @param int $fourIrInitiativeId
     * @return mixed
     */
    public function getFilePath(int $fourIrInitiativeId): mixed
    {
      return FourIRFileLog::where("four_ir_initiative_id", $fourIrInitiativeId)->latest()->first();

    }
}

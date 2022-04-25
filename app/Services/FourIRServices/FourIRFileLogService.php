<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRProject;

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
    public function storeFileLog(array $data, string $step)
    {
        if(!empty($data['file_path'])){
            $this->store($data, $step);
        }
    }

    /**
     * @param string $filePath
     * @param array $data
     * @param string $step
     * @return void
     */
    public function updateFileLog(string $filePath, array $data, string $step)
    {
        if(!empty($data['file_path']) && $filePath != $data['file_path']){
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
            'four_ir_project_id' => $data['id'],
            'file_path' => $data['file_path'],
            'module_type' => $step,
            'accessor_type' => $data['accessor_type'],
            'accessor_id' => $data['accessor_id'],
            'row_status' => $data['row_status'] ?? BaseModel::ROW_STATUS_ACTIVE
        ]);
        $fourIrFileLog->save();
    }
}

<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRProject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRFileLogService
 * @package App\Services
 */
class FourIRFileLogService
{
    /**
     * @param array $data
     * @return void
     */
    public function storeLog(array $data)
    {
        $fourIrFileLog = new FourIRFileLog();
        $fourIrFileLog->fill([
            'four_ir_project_id' => $data['four_ir_project_id'],
            'file_path' => $data['file_path'],
            'module_type' => FourIRProject::FILE_LOG_PROJECT_INITIATION_STEP,
            'accessor_type' => $data['accessor_type'],
            'accessor_id' => $data['accessor_id'],
            'row_status' => $data['row_status'] ?? BaseModel::ROW_STATUS_ACTIVE
        ]);
        $fourIrFileLog->save();
    }
}

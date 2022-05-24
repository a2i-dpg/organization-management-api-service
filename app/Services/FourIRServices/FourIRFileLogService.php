<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRInitiative;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FourIRFileLogService
 * @package App\Services
 */
class FourIRFileLogService
{
    public function getFileLogs(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $name = $request['name'] ?? "";
        $nameEn = $request['name_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "DESC";
        $step = $request['file_log_step'] ?? "";
        $fromDate = $request['from_date'] ?? "";
        $toDate = $request['to_date'] ?? "";
        $toDate = (new Carbon($toDate))->addDay();

        $fileLogBuilder = FourIRFileLog::select([
            "four_ir_file_logs.id",
            "four_ir_file_logs.file_path",
            "four_ir_file_logs.module_type",
            "four_ir_file_logs.four_ir_initiative_id",
            "four_ir_initiatives.name as four_ir_initiative_name",
            "four_ir_initiatives.name_en as four_ir_initiative_name_en",
            "four_ir_initiatives.organization_name as four_ir_initiative_organization_name",
            "four_ir_initiatives.organization_name_en as four_ir_initiative_organization_name_en",
            "four_ir_file_logs.accessor_type",
            "four_ir_file_logs.accessor_id",
            "four_ir_file_logs.row_status",
            "four_ir_file_logs.created_by",
            "four_ir_file_logs.updated_by",
            "four_ir_file_logs.created_at",
            "four_ir_file_logs.updated_at",
        ]);

        $fileLogBuilder->join("four_ir_initiatives", "four_ir_initiatives.id", "four_ir_file_logs.four_ir_initiative_id");
        $fileLogBuilder->orderBy('four_ir_file_logs.id', $order);

        if (!empty($name)) {
            $fileLogBuilder->where(function ($builder) use ($name) {
                $builder->where('four_ir_initiatives.name', 'like', '%' . $name . '%');
            });
        }

        if (!empty($nameEn)) {
            $fileLogBuilder->where(function ($builder) use ($nameEn) {
                $builder->where('four_ir_initiatives.name_en', 'like', '%' . $nameEn . '%');
            });
        }

        $fileLogBuilder->where(function ($builder) use ($step) {
            $builder->where('four_ir_file_logs.module_type', $step);
        });

        $fileLogBuilder->where(function ($builder) use ($fourIrInitiativeId) {
            $builder->where('four_ir_file_logs.four_ir_initiative_id', $fourIrInitiativeId);
        });

        if (is_numeric($rowStatus)) {
            $fileLogBuilder->where('four_ir_file_logs.row_status', $rowStatus);
        }

        if (!empty($fromDate) && empty($toDate)) {
            $fileLogBuilder->whereDate('four_ir_file_logs.created_at', $fromDate);
        } elseif (empty($fromDate) && !empty($toDate)) {
            $fileLogBuilder->whereDate('four_ir_file_logs.created_at', $fromDate);
        } elseif (!empty($fromDate) && !empty($toDate)) {
            $fileLogBuilder->whereBetween('four_ir_file_logs.created_at', [$fromDate, $toDate]);
        }

        /** @var Collection $fourIrTaglines */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fileLogs = $fileLogBuilder->paginate($pageSize);
            $paginateData = (object)$fileLogs->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fileLogs = $fileLogBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fileLogs->toArray()['data'] ?? $fileLogs->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
        return $response;

    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getFileLog(int $id): mixed
    {
        $fileLogBuilder = FourIRFileLog::select([
            "four_ir_file_logs.id",
            "four_ir_file_logs.file_path",
            "four_ir_file_logs.module_type",
            "four_ir_file_logs.four_ir_initiative_id",
            "four_ir_initiatives.name as four_ir_initiative_name",
            "four_ir_initiatives.name_en as four_ir_initiative_name_en",
            "four_ir_initiatives.organization_name as four_ir_initiative_organization_name",
            "four_ir_initiatives.organization_name_en as four_ir_initiative_organization_name_en",
            "four_ir_file_logs.accessor_type",
            "four_ir_file_logs.accessor_id",
            "four_ir_file_logs.row_status",
            "four_ir_file_logs.created_by",
            "four_ir_file_logs.updated_by",
            "four_ir_file_logs.created_at",
            "four_ir_file_logs.updated_at",
        ]);

        $fileLogBuilder->join("four_ir_initiatives", "four_ir_initiatives.id", "four_ir_file_logs.four_ir_initiative_id");
        return $fileLogBuilder->findOrFail($id);

    }

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
    public function getFilePath(int $fourIrInitiativeId, int $moduleType): mixed
    {
        return FourIRFileLog::where("four_ir_initiative_id", $fourIrInitiativeId)->where('module_type', $moduleType)->latest()->first();

    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            "name" => "nullable",
            "name_en" => "nullable",
            'four_ir_initiative_id' => 'required|int',
            'file_log_step' => 'required|int',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

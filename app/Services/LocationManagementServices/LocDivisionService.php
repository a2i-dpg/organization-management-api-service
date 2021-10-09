<?php

namespace App\Services\LocationManagementServices;

use App\Models\BaseModel;
use App\Models\LocDivision;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LocService
 * @package App\Services\Sevices
 */
class LocDivisionService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllDivisions(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $divisionsBuilder */
        $divisionsBuilder = LocDivision::select([
            'id',
            'title',
            'title_en',
            'bbs_code',
            'row_status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ]);
        $divisionsBuilder->orderBy('id', $order);

        if (is_numeric($rowStatus)) {
            $divisionsBuilder->where('row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $divisionsBuilder->where('title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $divisionsBuilder->where('title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $divisions */
        $divisions = $divisionsBuilder->get();

        $response['order'] = $order;
        $response['data'] = $divisions->toArray()['data'] ?? $divisions->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffForHumans(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneDivision(int $id, Carbon $startTime): array
    {
        /** @var LocDivision|Builder $divisionsBuilder */
        $divisionsBuilder = LocDivision::select([
            'id',
            'title',
            'title_en',
            'bbs_code',
            'row_status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ]);
        $divisionsBuilder->where('id', $id);

        /** @var  LocDivision $divisions */
        $divisions = $divisionsBuilder->first();

        return [
            "data" => $divisions ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffForHumans(Carbon::now())
            ]
        ];
    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:250|min:2',
            'title' => 'nullable|max:500|min:2',
            'order' => [
                'string',
                Rule::in([(BaseModel::ROW_ORDER_ASC), (BaseModel::ROW_ORDER_DESC)])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

<?php

namespace App\Services;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\OrganizationUnitType;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OrganizationUnitTypeService
 * @package App\Services
 */
class OrganizationUnitTypeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationUnitType(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationId = $request['organization_id'] ?? "";


        /** @var Builder $organizationUnitTypeBuilder */
        $organizationUnitTypeBuilder = OrganizationUnitType::select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title',
            'organization_unit_types.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title_bn',
            'organization_unit_types.row_status',
            'organization_unit_types.created_by',
            'organization_unit_types.updated_by',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',

        ])->byOrganization('organization_unit_types');

        $organizationUnitTypeBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('organization_unit_types.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }
        });

        $organizationUnitTypeBuilder->orderBy('organization_unit_types.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationUnitTypeBuilder->where('organization_unit_types.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $organizationUnitTypeBuilder->where('organization_unit_types.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $organizationUnitTypeBuilder->where('organization_unit_types.title', 'like', '%' . $titleBn . '%');
        }
        if (is_numeric($organizationId)) {
            $organizationUnitTypeBuilder->where('organization_unit_types.organization_id', $organizationId);
        }

        /** @var Collection $organizationUnitTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $organizationUnitTypes = $organizationUnitTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationUnitTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationUnitTypes = $organizationUnitTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationUnitTypes->toArray()['data'] ?? $organizationUnitTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOrganizationUnitType(int $id, Carbon $startTime): array
    {
        /** @var Builder $organizationUnitTypeBuilder */
        $organizationUnitTypeBuilder = OrganizationUnitType::select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title',
            'organization_unit_types.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title_bn',
            'organization_unit_types.row_status',
            'organization_unit_types.created_by',
            'organization_unit_types.updated_by',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',
        ]);

        $organizationUnitTypeBuilder->join('organizations', function ($join) {
            $join->on('organization_unit_types.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
        });
        $organizationUnitTypeBuilder->where('organization_unit_types.id', '=', $id);

        /**@var OrganizationUnitType $organizationUnitType * */
        $organizationUnitType = $organizationUnitTypeBuilder->first();

        return [
            "data" => $organizationUnitType ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ],
        ];
    }

    /**
     * @param array $data
     * @return OrganizationUnitType
     */
    public function store(array $data): OrganizationUnitType
    {
        $organizationUnitType = new OrganizationUnitType();
        $organizationUnitType->fill($data);
        $organizationUnitType->save();
        return $organizationUnitType;
    }

    /**
     * @param OrganizationUnitType $organizationUnitType
     * @param array $data
     * @return OrganizationUnitType
     */
    public function update(OrganizationUnitType $organizationUnitType, array $data): OrganizationUnitType
    {
        $organizationUnitType->fill($data);
        $organizationUnitType->save();
        return $organizationUnitType;
    }

    /**
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function destroy(OrganizationUnitType $organizationUnitType): bool
    {
        return $organizationUnitType->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllTrashedOrganizationUnitType(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $pageSize = $request->query('pageSize', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationUnitTypeBuilder */
        $organizationUnitTypeBuilder = OrganizationUnitType::onlyTrashed()->select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title',
            'organization_unit_types.organization_id',
            'organizations.title_en as organization_name',
            'organization_unit_types.row_status',
            'organization_unit_types.created_by',
            'organization_unit_types.updated_by',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',
        ]);
        $organizationUnitTypeBuilder->join('organizations', 'organization_unit_types.organization_id', '=', 'organizations.id');
        $organizationUnitTypeBuilder->orderBy('organization_unit_types.id', $order);

        if (!empty($titleEn)) {
            $organizationUnitTypeBuilder->where('$jobSectors.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitTypeBuilder->where('job_sectors.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizationUnitTypes */

        if (!is_null($paginate) || !is_null($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $organizationUnitTypes = $organizationUnitTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationUnitTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationUnitTypes = $organizationUnitTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationUnitTypes->toArray()['data'] ?? $organizationUnitTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function restore(OrganizationUnitType $organizationUnitType): bool
    {
        return $organizationUnitType->restore();
    }

    /**
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function forceDelete(OrganizationUnitType $organizationUnitType): bool
    {
        return $organizationUnitType->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max: 300',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max: 600',
                'min:2',
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
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
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max: 300|min:2',
            'title' => 'nullable|max: 600|min:2',
            'page' => 'integer|gt:0',
            'organization_id' => 'integer|exists:organizations,id',
            'pageSize' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

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
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $limit = array_key_exists('limit', $request) ? $request['limit'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";
        $organizationId = array_key_exists('organization_id', $request) ? $request['organization_id'] : "";


        /** @var Builder $organizationUnitTypeBuilder */
        $organizationUnitTypeBuilder = OrganizationUnitType::select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title_bn',
            'organization_unit_types.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title_bn as organization_title_bn',
            'organization_unit_types.row_status',
            'organization_unit_types.created_by',
            'organization_unit_types.updated_by',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',
        ]);
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
            $response['row_status'] = $rowStatus;
        }
        if (!empty($titleEn)) {
            $organizationUnitTypeBuilder->where('$jobSectors.title_en', 'like', '%' . $titleEn . '%');
        }
        elseif (!empty($titleBn)) {
            $organizationUnitTypeBuilder->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }
        elseif (!empty($organizationId)) {
            $organizationUnitTypeBuilder->where('organization_unit_types.organization_id', $organizationId);
        }


        echo $organizationUnitTypeBuilder->toSql();

        /** @var Collection $organizationUnitTypes */

        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: 10;
            $organizationUnitTypes = $organizationUnitTypeBuilder->paginate($limit);
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
        $response['response_status'] = [
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
            'organization_unit_types.title_bn',
            'organization_unit_types.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title_bn as organization_title_bn',
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
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationUnitTypeBuilder */
        $organizationUnitTypeBuilder = OrganizationUnitType::onlyTrashed()->select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title_bn',
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
            $organizationUnitTypeBuilder->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizationUnitTypes */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $organizationUnitTypes = $organizationUnitTypeBuilder->paginate($limit);
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
        $response['response_status'] = [
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
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max: 191',
                'min:2'
            ],
            'title_bn' => [
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
        return Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC',
            'row_status.in' => 'Row status must be within 1 or 0'
        ];
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'limit' => 'numeric',
            'organization_id'=>'numeric',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

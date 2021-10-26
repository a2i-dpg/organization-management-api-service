<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\OrganizationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OrganizationTypeService
 * @package App\Services
 */
class OrganizationTypeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationType(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->orderBy('organization_types.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationTypeBuilder->where('organization_types.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $organizationTypeBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $organizationTypeBuilder->where('organization_types.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $organizationTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizationTypes = $organizationTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationTypes = $organizationTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationTypes->toArray()['data'] ?? $organizationTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return OrganizationType
     */
    public function getOneOrganizationType(int $id): OrganizationType
    {
        /** @var OrganizationType|Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->where('organization_types.id', '=', $id);

        return $organizationTypeBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return OrganizationType
     */
    public function store(array $data): OrganizationType
    {
        $organizationType = new OrganizationType();
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;
    }

    /**
     * @param OrganizationType $organizationType
     * @param array $data
     * @return OrganizationType
     */
    public function update(OrganizationType $organizationType, array $data): OrganizationType
    {
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function destroy(OrganizationType $organizationType): bool
    {
        return $organizationType->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllTrashedOrganizationUnit(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('pageSize', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = $request->query('order', 'ASC');

        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::onlyTrashed()->select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->orderBy('organization_types.id', $order);

        if (!empty($titleEn)) {
            $organizationTypeBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $organizationTypeBuilder->where('organization_types.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $organizationTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizationTypes = $organizationTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationTypes = $organizationTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationTypes->toArray()['data'] ?? $organizationTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function restore(OrganizationType $organizationType): bool
    {
        return $organizationType->restore();
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function forceDelete(OrganizationType $organizationType): bool
    {
        return $organizationType->forceDelete();
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
                'row_status.in' => 'Row status must be within 1 or 0. [30000]'
            ]
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max:600',
                'min:2',
            ],
            'is_government' => [
                'nullable',
                'integer',
                Rule::in(OrganizationType::ORGANIZATION_TYPE_IS_GOVERNMENT_TRUE, OrganizationType::ORGANIZATION_TYPE_IS_GOVERNMENT_FALSE)
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([OrganizationType::ROW_STATUS_ACTIVE, OrganizationType::ROW_STATUS_INACTIVE]),
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
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:1',
            'page' => 'nullable|int|gt:0',
            'page_size' => 'nullable|int|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([OrganizationType::ROW_STATUS_ACTIVE, OrganizationType::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

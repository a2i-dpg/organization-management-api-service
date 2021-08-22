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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationType(Request $request, Carbon $startTime): array
    {
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title_bn',
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
        } elseif (!empty($titleBn)) {
            $organizationTypeBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizationTypes */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $organizationTypes = $organizationTypeBuilder->paginate($limit);
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
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "started" => $startTime->format('H i s'),
            "finished" => Carbon::now()->format('H i s')
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOrganizationType(int $id, carbon $startTime): array
    {
        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->where('organization_types.id', '=', $id);


        /** @var OrganizationType $organizationType */
        $organizationType = $organizationTypeBuilder->first();

        return [
            "data" => $organizationType ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];
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
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'max:191',
                'min:2',
                'required',
                'string'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:400',
                'min:2',
            ],
            'is_government' => [
                'required',
                'boolean'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

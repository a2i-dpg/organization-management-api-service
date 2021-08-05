<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var OrganizationType|Builder $organizationTypes */
        $organizationTypes = OrganizationType::select([
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
        $organizationTypes->orderBy('organization_types.id', $order);

        if (!empty($titleEn)) {
            $organizationTypes->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationTypes->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $organizationTypes = $organizationTypes->paginate(10);
            $paginateData = (object)$organizationTypes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink = $paginateData->links;
        } else {
            $organizationTypes = $organizationTypes->get();
        }

        $data = [];
        foreach ($organizationTypes as $organizationType) {
            $links['read'] = route('api.v1.organization-types.read', ['id' => $organizationType->id]);
            $links['update'] = route('api.v1.organization-types.update', ['id' => $organizationType->id]);
            $links['delete'] = route('api.v1.organization-types.destroy', ['id' => $organizationType->id]);
            $organizationType['_links'] = $links;
            $data[] = $organizationType->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.organization-types.get-list')
                ]
            ],
            "_page" => $page,
            "_order" => $order
        ];
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOrganizationType(int $id, carbon $startTime): array
    {
        /** @var OrganizationType|Builder $organizationType */
        $organizationType = OrganizationType::select([
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
        $organizationType->where('organization_types.id', '=', $id);
        $organizationType = $organizationType->first();

        $links = [];
        if (!empty($organizationType)) {
            $links = [
                'update' => route('api.v1.organization-types.update', ['id' => $id]),
                'delete' => route('api.v1.organization-types.destroy', ['id' => $id])
            ];
        }

        return [
            "data" => $organizationType ?: [],
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links
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
     * @return OrganizationType
     */
    public function destroy(OrganizationType $organizationType): OrganizationType
    {
        $organizationType->row_status = Organization::ROW_STATUS_DELETED;
        $organizationType->save();
        $organizationType->delete();
        return $organizationType;
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
                Rule::in([OrganizationType::ROW_STATUS_ACTIVE, OrganizationType::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

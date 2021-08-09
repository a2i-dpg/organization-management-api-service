<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\OrganizationUnitType;
use Illuminate\Database\Query\Builder;

/**
 * Class OrganizationUnitTypeService
 * @package App\Services
 */
class OrganizationUnitTypeService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationUnitType(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $organizationId = $request->query('organization_id');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var OrganizationUnitType|Builder $organizationUnitTypes */
        $organizationUnitTypes = OrganizationUnitType::select([
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
        $organizationUnitTypes->join('organizations', 'organization_unit_types.organization_id', '=', 'organizations.id')->orderBy('organization_unit_types.id', $order);

        if (!empty($titleEn)) {
            $organizationUnitTypes->where('organization_unit_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitTypes->where('organization_unit_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if (!empty($organizationId)) {
            $organizationUnitTypes->where('organization_unit_types.organization_id',$organizationId);
        }

        if ($paginate) {
            $organizationUnitTypes = $organizationUnitTypes->paginate(10);
            $paginateData = (object)$organizationUnitTypes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $organizationUnitTypes = $organizationUnitTypes->get();
        }
        $data = [];
        foreach ($organizationUnitTypes as $organizationUnitType) {
            $links['read'] = route('api.v1.organization-unit-types.read', ['id' => $organizationUnitType->id]);
            $links['edit'] = route('api.v1.organization-unit-types.update', ['id' => $organizationUnitType->id]);
            $links['delete'] = route('api.v1.organization-unit-types.destroy', ['id' => $organizationUnitType->id]);
            $organizationUnitType['_links'] = $links;
            $data[] = $organizationUnitType->toArray();
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
                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.organization-unit-types.get-list')
                ],
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
    public function getOneOrganizationUnitType(int $id, Carbon $startTime): array
    {
        /** @var OrganizationUnitType|Builder $organizationUnitType */
        $organizationUnitType = OrganizationUnitType::select([
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

        $organizationUnitType->join('organizations', 'organization_unit_types.organization_id', '=', 'organizations.id');$organizationUnitType->where('organization_unit_types.row_status', '=', OrganizationUnitType::ROW_STATUS_ACTIVE);
        $organizationUnitType->where('organization_unit_types.id', '=', $id);
        $organizationUnitType = $organizationUnitType->first();

        $links = [];
        if (!empty($organizationUnitType)) {
            $links['update'] = route('api.v1.organization-unit-types.update', ['id' => $id]);
            $links['delete'] = route('api.v1.organization-unit-types.destroy', ['id' => $id]);
        }
        return [
            "data" => $organizationUnitType ? $organizationUnitType : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
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
                Rule::in([OrganizationUnitType::ROW_STATUS_ACTIVE, OrganizationUnitType::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

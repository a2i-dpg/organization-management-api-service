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
     * @return array
     */
    public function getAllOrganizationUnitType(Request $request, Carbon $startTime): array
    {
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var OrganizationUnitType|Builder $organizationUnitTypes */
        $organizationUnitTypes = OrganizationUnitType::select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title_bn',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',
            'organization_unit_types.row_status',
            'organizations.title_en as organization_name',
        ]);
        $organizationUnitTypes->join('organizations', 'organization_unit_types.organization_id', '=', 'organizations.id')->orderBy('organization_unit_types.id', $order);

        if (!empty($titleEn)) {
            $organizationUnitTypes->where('$jobSectors.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitTypes->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $organizationUnitTypes = $organizationUnitTypes->paginate(10);
            $paginate_data = (object)$organizationUnitTypes->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $organizationUnitTypes = $organizationUnitTypes->get();
        }
        $data = [];
        foreach ($organizationUnitTypes as $organizationUnitType) {
            $_links['read'] = route('api.v1.organization-unit-types.read', ['id' => $organizationUnitType->id]);
            $_links['edit'] = route('api.v1.organization-unit-types.update', ['id' => $organizationUnitType->id]);
            $_links['delete'] = route('api.v1.organization-unit-types.destroy', ['id' => $organizationUnitType->id]);
            $_link['_links'] = $_links;
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
                'paginate' => $paginate_link,
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
     * @param $id
     * @return array
     */
    public function getOneOrganizationUnitType(int $id, Carbon $startTime): array
    {
        /** @var OrganizationUnitType|Builder $organizationUnitType */
        $organizationUnitType = OrganizationUnitType::select([
            'organization_unit_types.id',
            'organization_unit_types.title_en',
            'organization_unit_types.title_bn',
            'organization_unit_types.created_at',
            'organization_unit_types.updated_at',
            'organization_unit_types.row_status',
            'organizations.title_en as organization_name',
        ]);

        $organizationUnitType->join('organizations', 'organization_unit_types.organization_id', '=', 'organizations.id');$organizationUnitType->where('organization_unit_types.row_status', '=', OrganizationUnitType::ROW_STATUS_ACTIVE);
        $organizationUnitType->where('organization_unit_types.id', '=', $id);
        $organizationUnitType = $organizationUnitType->first();

        $links = [];
        if (!empty($organizationUnitType)) {
            $links['update'] = route('api.v1.organization-unit-types.update', ['id' => $id]);
            $links['delete'] = route('api.v1.organization-unit-types.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $organizationUnitType ? $organizationUnitType : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;
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
     * @return OrganizationUnitType
     */
    public function destroy(OrganizationUnitType $organizationUnitType): OrganizationUnitType
    {
        $organizationUnitType->row_Status =OrganizationUnitType::ROW_STATUS_DELETED;
        $organizationUnitType->save();
        return $organizationUnitType;
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max: 191'
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191'
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'row_status' => [
                Rule::requiredIf(function () use ($id) {
                    return !empty($id);
                }),
                'int',
            ]
        ];

        return Validator::make($request->all(), $rules);

    }

}

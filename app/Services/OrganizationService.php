<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

/**
 * Class OrganizationService
 * @package App\Services
 */
class OrganizationService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganization(Request $request, Carbon $startTime): array
    {

        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder organizationBuilder */
        $organizationBuilder = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
            'organizations.domain',
            'organizations.fax_no',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organizations.contact_person_mobile',
            'organizations.contact_person_email',
            'organizations.contact_person_designation',
            'organizations.description',
            'organizations.address',
            'organizations.logo',
            'organizations.loc_division_id',
            'organizations.loc_district_id',
            'organizations.loc_upazila_id',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_types_title',
            'organizations.row_status',
            'organizations.created_by',
            'organizations.updated_by',
            'organizations.created_at',
            'organizations.updated_at'
        ]);
        $organizationBuilder->join('organization_types', 'organizations.organization_type_id', '=', 'organization_types.id');
        $organizationBuilder->orderBy('organizations.id', $order);

        if (!empty($titleEn)) {
            $organizationBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizations */

        if ($paginate) {
            $organizations = $organizationBuilder->paginate($limit);
            $paginateData = (object)$organizations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizations = $organizationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizations->toArray()['data'] ?? $organizations->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => JsonResponse::HTTP_OK,
            "started" => $startTime->format('H i s'),
            "finished" => Carbon::now()->format('H i s'),
        ];

        return $response;

    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOrganization(int $id, Carbon $startTime): array
    {
        /** @var Builder $organizationBuilder */
        $organizationBuilder = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
            'organizations.domain',
            'organizations.fax_no',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organizations.contact_person_mobile',
            'organizations.contact_person_email',
            'organizations.contact_person_designation',
            'organizations.description',
            'organizations.address',
            'organizations.logo',
            'organizations.loc_division_id',
            'organizations.loc_district_id',
            'organizations.loc_upazila_id',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_types_title',
            'organizations.row_status',
            'organizations.created_by',
            'organizations.updated_by',
            'organizations.created_at',
            'organizations.updated_at'
        ]);
        $organizationBuilder->join('organization_types', 'organizations.organization_type_id', '=', 'organization_types.id');
        $organizationBuilder->where('organizations.id', '=', $id);


        /** @var Organization $organization */
        $organization = $organizationBuilder->first();

        return [
            "data" => $organization ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
        ];
    }

    /**
     * @param array $data
     * @return Organization
     */
    public function store(array $data): Organization
    {
        $organization = new Organization();
        $organization->fill($data);
        $organization->save();
        return $organization;
    }

    /**
     * @param Organization $organization
     * @param array $data
     * @return Organization
     */
    public function update(Organization $organization, array $data): Organization
    {
        $organization->fill($data);
        $organization->save();
        return $organization;
    }

    /**
     * @param Organization $organization
     * @return bool
     */
    public function destroy(Organization $organization): bool
    {
        return $organization->delete();
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
                'max:300',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            'organization_type_id' => [
                'required',
                'int'
            ],
            'domain' => [
                'required',
                'string',
                'max:191',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'unique:organizations,domain,' . $id
            ],
            'description' => [
                'nullable',
                'max:5000',
            ],
            'fax_no' => [
                'nullable',
                'max: 50',
            ],
            'loc_division_id' => [
                'nullable',
                'int',
            ],
            'loc_district_id' => [
                'nullable',
                'int',
            ],
            'loc_upazila_id' => [
                'nullable',
                'int',
            ],
            'contact_person_mobile' => [
                'required',
                'regex: /^(?:\+88|88)?(01[3-9]\d{8})$/',
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_designation' => [
                'required',
                'max: 300',
                "min:2"
            ],
            'contact_person_email' => [
                'required',
                'regex: /\S+@\S+\.\S+/'
            ],
            'mobile' => [
                'required',
                'regex: /^(?:\+88|88)?(01[3-9]\d{8})$/',
            ],
            'email' => [
                'required',
                'regex : /^[^\s@]+@[^\s@]+$/',
            ],
            'logo' => [
                'required_if:' . $id . ',null',
                'string',
            ],
            'address' => [
                'required',
                'max: 1000',
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Organization::ROW_STATUS_ACTIVE, Organization::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

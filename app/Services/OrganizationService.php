<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OrganizationService
 * @package App\Services
 */
class OrganizationService
{

    public function OrganizationsList(Request $request)
    {
        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();
        $paginate = $request->query('page');
        $organizations = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organization_types.title_en as organization_types_title',

        ]);
        $organizations->join('organization_types', 'organizations.organization_type_id', '=', 'organization_types.id');

        if ($paginate) {
            $organizations = $organizations->paginate(1);
            $paginate_data = (object)$organizations->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link = $paginate_data->links;
        } else {
            $organizations = $organizations->get();
        }

        foreach ($organizations as $organization) {
            $action['view'] = route('api.v1.organizations.view', ['id' => $organization->id]);
            $action['edit'] = route('api.v1.organizations.update', ['id' => $organization->id]);
            $action['delete'] = route('api.v1.organizations.destroy', ['id' => $organization->id]);
            $organization['action'] = $action;
            $data[] = $organization->toArray();

        }

        $response = [
            "data" => $data,
            "response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "links" => [
                'paginate' => $paginate_link,
                'link' => route('api.v1.organizations.index')
            ],

            "page" => $page,
        ];


        return $response;
    }

    public function singleOrganization($id)
    {

        $startTime = Carbon::now();
        $organization = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organization_types.title_en as organization_types_title',

        ]);
        $organization->join('organization_types', 'organizations.organization_type_id', '=', 'organization_types.id');
        $organization->where('organizations.id','=',$id);
        $organization = $organization->first();
        $action = [];
        if (!empty($organization)) {
            $action['edit'] = route('api.v1.organizations.update', ['id' => $id]);
            $action['delete'] = route('api.v1.organizations.destroy', ['id' => $id]);
        }

        $response = [
            "data" => $organization ? $organization : [],
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "action" => $action,
        ];
        return $response;

    }


}

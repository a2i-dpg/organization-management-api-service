<?php


namespace App\Services;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class ServiceService
 * @package App\Services
 */
class ServiceService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getServiceList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $services = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'organizations.title_en as organization_title_en',
                'services.row_status',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $services->join('organizations', 'services.organization_id', '=', 'organizations.id');

        if (!empty($titleEn)) {
            $services->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $services->where('services.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $services = $services->paginate(10);
            $paginate_data = (object)$services->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $services = $services->get();
        }

        $data = [];

        foreach ($services as $service) {
            $_links['read'] = route('api.v1.services.read', ['id' => $service->id]);
            $_links['update'] = route('api.v1.services.update', ['id' => $service->id]);
            $_links['delete'] = route('api.v1.services.destroy', ['id' => $service->id]);
            $service['_links'] = $_links;
            $data[] = $service->toArray();
        }
        $response = [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => [
                'paginate' => $paginate_link,
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.services.get-list')

                ],

            ],

            "_page" => $page,
            "_order" => $order
        ];

        return $response;

    }

    /**
     * @param $id
     * @return array
     */
    public function getOneService($id)
    {
        $startTime = Carbon::now();
        $service = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'organizations.title_en as organization_title_en',
                'services.row_status',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $service->join('organizations', 'services.organization_id', '=', 'organizations.id')
            ->where('services.row_status', '=', Service::ROW_STATUS_ACTIVE)
            ->where('services.id', '=', $id);
        $service = $service->first();

        $links = [];
        if (!empty($service)) {
            $links['update'] = route('api.v1.services.update', ['id' => $id]);
            $links['delete'] = route('api.v1.services.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $service ? $service : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;
    }

    /**
     * @param array $data
     * @return Service
     */
    public function store(array $data): Service
    {
        $service = new Service();
        $service->fill($data);
        $service->save();

        return $service;
    }

    /**
     * @param Service $service
     * @param array $data
     * @return Service
     */
    public function update(Service $service, array $data): Service
    {
        $service->fill($data);
        $service->save();

        return $service;
    }

    /**
     * @param Service $service
     * @return Service
     */
    public function destroy(Service $service): Service
    {
        $service->row_status = 99;
        $service->save();

        return $service;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191',
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
        ];

        return Validator::make($request->all(), $rules);
    }

}

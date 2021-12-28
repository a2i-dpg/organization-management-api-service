<?php


namespace App\Services;

use App\Models\AreaOfBusiness;
use App\Models\BaseModel;
use App\Models\JobSector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JobSectorService
 * @package App\Services
 */
class JobSectorService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getJobSectorList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title',
                'job_sectors.row_status',
                'job_sectors.created_by',
                'job_sectors.updated_by',
                'job_sectors.created_at',
                'job_sectors.updated_at'
            ]
        );
        $jobSectorBuilder->orderBy('job_sectors.id', $order);

        if (is_numeric($rowStatus)) {
            $jobSectorBuilder->where('job_sectors.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $jobSectorBuilder->where('job_sectors.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $jobSectorBuilder->where('job_sectors.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $jobSectors */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $jobSectors = $jobSectorBuilder->paginate($pageSize);
            $paginateData = (object)$jobSectors->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobSectors = $jobSectorBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobSectors->toArray()['data'] ?? $jobSectors->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }




    public function getAreaOfBusinessList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        $areaOfBusinessBuilder = AreaOfBusiness::select(
           [
               'area_of_business.id',
               'area_of_business.title',
               'area_of_business.created_at',
               'area_of_business.updated_at'
           ]
        );

        $areaOfBusinessBuilder->orderBy('area_of_business.id', $order);
        $areaOfBusinessBuilder->where('area_of_business.row_status', $rowStatus);

        if (!empty($title)) {
            $areaOfBusinessBuilder->where('area_of_business.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $jobSectors */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $areaOfBusiness = $areaOfBusinessBuilder->paginate($pageSize);
            $paginateData = (object)$areaOfBusiness->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $areaOfBusiness = $areaOfBusinessBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $areaOfBusiness->toArray()['data'] ?? $areaOfBusiness->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }


    /**
     * @param int $id
     * @return JobSector
     */
    public function getOneJobSector(int $id): JobSector
    {
        /** @var JobSector| Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title',
                'job_sectors.row_status',
                'job_sectors.created_by',
                'job_sectors.updated_by',
                'job_sectors.created_at',
                'job_sectors.updated_at'
            ]
        );
        $jobSectorBuilder->where('job_sectors.id', '=', $id);

        return $jobSectorBuilder->firstOrFail();

    }

    /**
     * @param array $data
     * @return JobSector
     */
    public function store(array $data): JobSector
    {
        $jobSector = new JobSector();
        $jobSector->fill($data);
        $jobSector->save();
        return $jobSector;
    }

    /**
     * @param JobSector $JobSector
     * @param array $data
     * @return JobSector
     */
    public function update(JobSector $JobSector, array $data): JobSector
    {
        $JobSector->fill($data);
        $JobSector->save();
        return $JobSector;
    }

    /**
     * @param JobSector $JobSector
     * @return bool
     */
    public function destroy(JobSector $JobSector): bool
    {
        return $JobSector->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedJobSectorList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('page_size', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::onlyTrashed()->select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title',
                'job_sectors.row_status',
                'job_sectors.created_by',
                'job_sectors.updated_by',
                'job_sectors.created_at',
                'job_sectors.updated_at'
            ]
        );
        $jobSectorBuilder->orderBy('job_sectors.id', $order);

        if (!empty($titleEn)) {
            $jobSectorBuilder->where('$jobSectorBuilder.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $jobSectorBuilder->where('job_sectors.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $jobSectors */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $jobSectors = $jobSectorBuilder->paginate($pageSize);
            $paginateData = (object)$jobSectors->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobSectors = $jobSectorBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobSectors->toArray()['data'] ?? $jobSectors->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param JobSector $jobSector
     * @return bool
     */
    public function restore(JobSector $jobSector): bool
    {
        return $jobSector->restore();
    }

    /**
     * @param JobSector $jobSector
     * @return bool
     */
    public function forceDelete(JobSector $jobSector): bool
    {
        return $jobSector->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
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
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([JobSector::ROW_STATUS_ACTIVE, JobSector::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }


    public function filterAreaOfBusinessValidator(Request $request) :\Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'title' => 'nullable|max:500|min:2'
        ]);

    }

    public function filterEducationInstitutionValidator(Request $request) :\Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'nullable|max:500|min:2'
        ]);
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
            'title' => 'nullable|max:500|min:2',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([JobSector::ROW_STATUS_ACTIVE, JobSector::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

<?php


namespace App\Services;

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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getJobSectorList(Request $request, Carbon $startTime): array
    {
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
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
        } elseif (!empty($titleBn)) {
            $jobSectorBuilder->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $jobSectors */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $jobSectors = $jobSectorBuilder->paginate($limit);
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
    public function getOneJobSector(int $id, Carbon $startTime): array
    {
        /** @var Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
                'job_sectors.created_by',
                'job_sectors.updated_by',
                'job_sectors.created_at',
                'job_sectors.updated_at'
            ]
        );
        $jobSectorBuilder->where('job_sectors.id', '=', $id);

        /** @var JobSector $jobSector */
        $jobSector = $jobSectorBuilder->first();

        return [
            "data" => $jobSector ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
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
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $jobSectorBuilder */
        $jobSectorBuilder = JobSector::onlyTrashed()->select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
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
        } elseif (!empty($titleBn)) {
            $jobSectorBuilder->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $jobSectors */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $jobSectors = $jobSectorBuilder->paginate($limit);
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
        $response['response_status'] = [
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
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:500',
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

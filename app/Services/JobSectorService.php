<?php


namespace App\Services;


use App\Models\JobSector;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var JobSector|Builder $jobSectors */
        $jobSectors = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
            ]
        );
        $jobSectors->orderBy('job_sectors.id', $order);
        if (!empty($titleEn)) {
            $jobSectors->where('$jobSectors.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $jobSectors->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $jobSectors = $jobSectors->paginate(10);
            $paginateData = (object)$jobSectors->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $jobSectors = $jobSectors->get();
        }
        $data = [];

        foreach ($jobSectors as $jobSector) {
            $links['read'] = route('api.v1.job-sectors.read', ['id' => $jobSector->id]);
            $links['edit'] = route('api.v1.job-sectors.update', ['id' => $jobSector->id]);
            $links['delete'] = route('api.v1.job-sectors.destroy', ['id' => $jobSector->id]);
            $_link['links'] = $links;
            $data[] = $jobSector->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "links" => [
                'paginate' => $paginateLink,
                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.job-sectors.get-list')
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
    public function getOneJobSector(int $id,Carbon $startTime): array
    {
        /** @var JobSector|Builder $jobSector */
        $jobSector = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
            ]
        );
            $jobSector->where('job_sectors.row_status', '=', JobSector::ROW_STATUS_ACTIVE);
            $jobSector->where('job_sectors.id', '=', $id);

        $jobSector = $jobSector->first();

        $links = [];
        if (!empty($jobSector)) {
            $links['update'] = route('api.v1.job-sectors.update', ['id' => $id]);
            $links['delete'] = route('api.v1.job-sectors.destroy', ['id' => $id]);
        }
        return [
            "data" => $jobSector ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "links" => $links,
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
     * @return JobSector
     */
    public function destroy(JobSector $JobSector): JobSector
    {
        $JobSector->row_status = JobSector::ROW_STATUS_DELETED;
        $JobSector->save();
        return $JobSector;
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
                'max:191',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([JobSector::ROW_STATUS_ACTIVE, JobSector::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}

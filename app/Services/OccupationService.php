<?php

namespace App\Services;

use App\Models\Occupation;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class OccupationService
 * @package App\Services
 */
class OccupationService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getOccupationList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Occupation|Builder $occupations */
        $occupations = Occupation::select([
            'occupations.id',
            'occupations.title_en',
            'occupations.title_bn',
            'occupations.job_sector_id',
            'job_sectors.title_en as job_sector_title',
            'occupations.row_status',
            'occupations.created_by',
            'occupations.updated_by',
            'occupations.created_at',
            'occupations.updated_at',
        ]);
        $occupations->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id');
        $occupations->orderBy('occupations.id', $order);

        if (!empty($titleEn)) {
            $occupations->where('occupations.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $occupations->where('occupations.title_en', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $occupations = $occupations->paginate(10);
            $paginateData = (object)$occupations->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink = $paginateData->links;
        } else {
            $occupations = $occupations->get();
        }

        $data = [];
        foreach ($occupations as $occupation) {
            $links['read'] = route('api.v1.occupations.read', ['id' => $occupation->id]);
            $links['update'] = route('api.v1.occupations.update', ['id' => $occupation->id]);
            $links['delete'] = route('api.v1.occupations.destroy', ['id' => $occupation->id]);
            $occupation['_links'] = $links;
            $data[] = $occupation->toArray();
        }

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "links" => [
                'paginate' => $paginateLink,
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.occupations.get-list')
                ]
            ],
            "_page" => $page,
            "_order" => $order
        ];
    }

    /**
     * @param $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOccupation($id, Carbon $startTime): array
    {
        $links = [];

        /** @var Occupation|Builder $occupation */
        $occupation = Occupation::select([
            'occupations.id',
            'occupations.title_en',
            'occupations.title_bn',
            'occupations.job_sector_id',
            'job_sectors.title_en as job_sector_title',
            'occupations.row_status',
            'occupations.created_by',
            'occupations.updated_by',
            'occupations.created_at',
            'occupations.updated_at',
        ]);
        $occupation->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id');
        $occupation->where('occupations.id', '=', $id);
        $occupation = $occupation->first();

        if (!empty($occupation)) {
            $links['update'] = route('api.v1.occupations.update', ['id' => $id]);
            $links['delete'] = route('api.v1.occupations.destroy', ['id' => $id]);
        }

        return [
            "data" => $occupation ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "links" => $links
        ];
    }

    /**
     * @param array $data
     * @return Occupation
     */
    public function store(array $data): Occupation
    {
        $occupation = new Occupation();
        $occupation->fill($data);
        $occupation->save();
        return $occupation;
    }

    /**
     * @param Occupation $occupation
     * @param array $data
     * @return Occupation
     */
    public function update(Occupation $occupation, array $data): Occupation
    {
        $occupation->fill($data);
        $occupation->save();
        return $occupation;
    }

    /**
     * @param Occupation $occupation
     * @return Occupation
     */
    public function destroy(Occupation $occupation): Occupation
    {
        $occupation->row_status = Occupation::ROW_STATUS_DELETED;
        $occupation->save();
        $occupation->delete();
        return $occupation;
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
                'required',
                'string'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:191',
            ],
            'job_sector_id' => [
                'required',
                'exists:job_sectors,id'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Occupation::ROW_STATUS_ACTIVE, Occupation::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

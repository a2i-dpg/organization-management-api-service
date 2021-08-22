<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $occupationBuilder */
        $occupationBuilder = Occupation::select([
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
        $occupationBuilder->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id');
        $occupationBuilder->orderBy('occupations.id', $order);

        if (!empty($titleEn)) {
            $occupationBuilder->where('occupations.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $occupationBuilder->where('occupations.title_en', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $occupations */

        if ($paginate) {
            $occupations = $occupationBuilder->paginate($limit);
            $paginateData = (object)$occupations->toArray();

            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $occupations = $occupationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $occupations->toArray()['data'] ?? $occupations->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "started" => $startTime,
            "finished" => Carbon::now()->format('s'),
        ];

        return $response;
    }

    /**
     * @param $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOccupation($id, Carbon $startTime): array
    {
        /** @var Builder $occupationBuilder */
        $occupationBuilder = Occupation::select([
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
        $occupationBuilder->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id');
        $occupationBuilder->where('occupations.id', '=', $id);

        /** @var  Occupation $occupation */
        $occupation = $occupationBuilder->first();

        return [
            "data" => $occupation ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
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
     * @return bool
     */
    public function destroy(Occupation $occupation): bool
    {
        return $occupation->delete();
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
                'max:200',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max:800',
                'min:2'
            ],
            'job_sector_id' => [
                'required',
                'int',
                'exists:job_sectors,id'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

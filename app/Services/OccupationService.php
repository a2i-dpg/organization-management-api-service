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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getOccupationList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";


        /** @var Builder $occupationBuilder */
        $occupationBuilder = Occupation::select([
            'occupations.id',
            'occupations.title_en',
            'occupations.title_bn',
            'occupations.job_sector_id',
            'job_sectors.title_en as job_sector_title_en',
            'job_sectors.title_bn as job_sector_title_bn',
            'occupations.row_status',
            'occupations.created_by',
            'occupations.updated_by',
            'occupations.created_at',
            'occupations.updated_at',
        ]);
        $occupationBuilder->join('job_sectors', function ($join) use ($rowStatus) {
            $join->on('occupations.job_sector_id', '=', 'job_sectors.id')
                ->whereNull('job_sectors.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('job_sectors.row_status', $rowStatus);
            }
        });
        $occupationBuilder->orderBy('occupations.id', $order);

        if (is_numeric($rowStatus)) {
            $occupationBuilder->where('occupations.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $occupationBuilder->where('occupations.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $occupationBuilder->where('occupations.title_en', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $occupations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $occupations = $occupationBuilder->paginate($pageSize);
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
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
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
            'job_sectors.title_en as job_sector_title_en',
            'job_sectors.title_bn as job_sector_title_bn',
            'occupations.row_status',
            'occupations.created_by',
            'occupations.updated_by',
            'occupations.created_at',
            'occupations.updated_at',
        ]);
        $occupationBuilder->join('job_sectors', function ($join) {
            $join->on('occupations.job_sector_id', '=', 'job_sectors.id')
                ->whereNull('job_sectors.deleted_at');
        });
        $occupationBuilder->where('occupations.id', '=', $id);

        /** @var  Occupation $occupation */
        $occupation = $occupationBuilder->first();

        return [
            "data" => $occupation ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
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
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedOccupationList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $page_size = $request->query('page_size', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $occupationBuilder */
        $occupationBuilder = Occupation::onlyTrashed()->select([
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

        if (!is_null($paginate) || !is_null($page_size)) {
            $page_size = $page_size ?: 10;
            $occupations = $occupationBuilder->paginate($page_size);
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
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Occupation $occupation
     * @return bool
     */
    public function restore(Occupation $occupation): bool
    {
        return $occupation->restore();
    }

    /**
     * @param Occupation $occupation
     * @return bool
     */
    public function forceDelete(Occupation $occupation): bool
    {
        return $occupation->forceDelete();
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


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC',
            'row_status.in' => 'Row status must be within 1 or 0'
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'page_size' => 'numeric',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

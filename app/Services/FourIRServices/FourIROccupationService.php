<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIROccupation;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FourIROccupationService
 * @package App\Services\FourIRProjectServices
 */
class FourIROccupationService
{


    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIROccupationList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder fourIROccupationBuilder */
        $fourIROccupationBuilder = FourIROccupation::select(
            [
                'four_ir_occupations.id',
                'four_ir_occupations.title_en',
                'four_ir_occupations.title',
                'four_ir_occupations.row_status',
                'four_ir_occupations.created_by',
                'four_ir_occupations.updated_by',
                'four_ir_occupations.created_at',
                'four_ir_occupations.updated_at',
            ]
        );

        $fourIROccupationBuilder->orderBy('four_ir_occupations.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIROccupationBuilder->where('four_ir_occupations.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $fourIROccupationBuilder->where('four_ir_occupations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $fourIROccupationBuilder->where('four_ir_occupations.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $ranks = $fourIROccupationBuilder->paginate($pageSize);
            $paginateData = (object)$ranks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ranks = $fourIROccupationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ranks->toArray()['data'] ?? $ranks->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIROccupation
     */
    public function getOneFourIROccupation(int $id): FourIROccupation
    {
        /** @var FourIROccupation|Builder $fourIrOccupationBuilder */
        $fourIrOccupationBuilder = FourIROccupation::select(
            [
                'four_ir_occupations.id',
                'four_ir_occupations.title_en',
                'four_ir_occupations.title',
                'four_ir_occupations.row_status',
                'four_ir_occupations.created_by',
                'four_ir_occupations.updated_by',
                'four_ir_occupations.created_at',
                'four_ir_occupations.updated_at'
            ]
        );

        $fourIrOccupationBuilder->where('four_ir_occupations.id', '=', $id);

        return $fourIrOccupationBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @return FourIROccupation
     */
    public function store(array $data): FourIROccupation
    {
        return FourIROccupation::updateOrCreate($data);
    }


    /**
     * @param FourIROccupation $fourIrOccupation
     * @param array $data
     * @return FourIROccupation
     */
    public function update(FourIROccupation $fourIrOccupation, array $data): FourIROccupation
    {
        $fourIrOccupation->fill($data);
        $fourIrOccupation->save();
        return $fourIrOccupation;
    }

    /**
     * @param FourIROccupation $fourIrOccupation
     * @return bool
     */

    public function destroy(FourIROccupation $fourIrOccupation): bool
    {
        return $fourIrOccupation->delete();
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
            'title' => [
                'required',
                'string'
            ],
            'title_en' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
        return Validator::make($request->all(), $rules);
    }

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
            'title_en' => 'nullable|max:400|min:2',
            'title' => 'nullable|max:800|min:2',
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
                Rule::in([Occupation::ROW_STATUS_ACTIVE, Occupation::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

}

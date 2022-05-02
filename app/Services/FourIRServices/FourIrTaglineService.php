<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRTagline;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class RankService
 * @package App\Services
 */
class FourIrTaglineService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIRTaglineList(array $request, Carbon $startTime): array
    {
        $name = $request['name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrTaglineBuilder */
        $fourIrTaglineBuilder = FourIRTagline::select(
            [
                'four_ir_taglines.id',
                'four_ir_taglines.name',
                'four_ir_taglines.name_en',
                'four_ir_taglines.start_date',
                'four_ir_taglines.accessor_type',
                'four_ir_taglines.accessor_id',
                'four_ir_taglines.row_status',
                'four_ir_taglines.created_by',
                'four_ir_taglines.updated_by',
                'four_ir_taglines.created_at',
                'four_ir_taglines.updated_at'
            ]
        )->acl();

        $fourIrTaglineBuilder->orderBy('four_ir_taglines.id', $order);

        if (!empty($name)) {
            $fourIrTaglineBuilder->where(function ($builder) use($name){
                $builder->where('four_ir_taglines.name', 'like', '%' . $name . '%');
                $builder->orWhere('four_ir_taglines.name_en', 'like', '%' . $name . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrTaglineBuilder->where('four_ir_taglines.row_status', $rowStatus);
        }

        /** @var Collection $fourIrTaglines */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrTaglines = $fourIrTaglineBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrTaglines->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrTaglines = $fourIrTaglineBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrTaglines->toArray()['data'] ?? $fourIrTaglines->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRTagline
     */
    public function getOneFourIRTagline(int $id): FourIRTagline
    {
        /** @var FourIRTagline|Builder $fourIrTaglineBuilder */
        $fourIrTaglineBuilder = FourIRTagline::select(
            [
                'four_ir_taglines.id',
                'four_ir_taglines.name',
                'four_ir_taglines.name_en',
                'four_ir_taglines.start_date',
                'four_ir_taglines.accessor_type',
                'four_ir_taglines.accessor_id',
                'four_ir_taglines.row_status',
                'four_ir_taglines.created_by',
                'four_ir_taglines.updated_by',
                'four_ir_taglines.created_at',
                'four_ir_taglines.updated_at'
            ]
        );
        $fourIrTaglineBuilder->where('four_ir_taglines.id', '=', $id);

        return $fourIrTaglineBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRTagline
     */
    public function store(array $data): FourIRTagline
    {
        $fourIrTagline = new FourIRTagline();
        $fourIrTagline->fill($data);
        $fourIrTagline->save();
        return $fourIrTagline;
    }

    /**
     * @param FourIRTagline $fourIrTagline
     * @param array $data
     * @return FourIRTagline
     */
    public function update(FourIRTagline $fourIrTagline, array $data): FourIRTagline
    {
        $fourIrTagline->fill($data);
        $fourIrTagline->save();
        return $fourIrTagline;
    }

    /**
     * @param FourIRTagline $fourIrTagline
     * @return bool
     */
    public function destroy(FourIRTagline $fourIrTagline): bool
    {
        return $fourIrTagline->delete();
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
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'start_date' => [
                'required',
                'date-format:Y-m-d'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
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
            'name' => 'nullable|max:600|min:2',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'start_date' => 'nullable|date',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRProjectCs;
use App\Models\FourIRProjectCurriculum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class RankService
 * @package App\Services
 */
class FourIRProjectCurriculumService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectCurriculumList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCurriculumBuilder */
        $fourIrProjectCurriculumBuilder = FourIRProjectCurriculum::select(
            [
                'four_ir_project_curriculums.id',
                'four_ir_project_curriculums.four_ir_initiative_id',
                'four_ir_project_curriculums.file_path',
                'four_ir_project_curriculums.accessor_type',
                'four_ir_project_curriculums.accessor_id',
                'four_ir_project_curriculums.row_status',
                'four_ir_project_curriculums.created_by',
                'four_ir_project_curriculums.updated_by',
                'four_ir_project_curriculums.created_at',
                'four_ir_project_curriculums.updated_at'
            ]
        )->acl();

        $fourIrProjectCurriculumBuilder->orderBy('four_ir_project_curriculums.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrProjectCurriculumBuilder->where('four_ir_project_curriculums.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjectCurriculums */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjectCurriculums = $fourIrProjectCurriculumBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjectCurriculums->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjectCurriculums = $fourIrProjectCurriculumBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrProjectCurriculums->toArray()['data'] ?? $fourIrProjectCurriculums->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRProjectCurriculum
     */
    public function getOneFourIrProjectCurriculum(int $id): FourIRProjectCurriculum
    {
        /** @var FourIRProjectCurriculum|Builder $fourIrProjectCurriculumBuilder */
        $fourIrProjectCurriculumBuilder = FourIRProjectCurriculum::select(
            [
                'four_ir_project_curriculums.id',
                'four_ir_project_curriculums.four_ir_initiative_id',
                'four_ir_project_curriculums.file_path',
                'four_ir_project_curriculums.accessor_type',
                'four_ir_project_curriculums.accessor_id',
                'four_ir_project_curriculums.row_status',
                'four_ir_project_curriculums.created_by',
                'four_ir_project_curriculums.updated_by',
                'four_ir_project_curriculums.created_at',
                'four_ir_project_curriculums.updated_at'
            ]
        );
        $fourIrProjectCurriculumBuilder->where('four_ir_project_curriculums.id', '=', $id);

        return $fourIrProjectCurriculumBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRProjectCurriculum
     */
    public function storeOrUpdate(array $data): FourIRProjectCurriculum
    {
        return FourIRProjectCurriculum::updateOrCreate(['four_ir_initiative_id' => $data['four_ir_initiative_id']], $data);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws Throwable
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if(!empty($request->input('four_ir_initiative_id'))){
            $projectCS = FourIRProjectCs::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))->first();
            throw_if(empty($projectCS), ValidationException::withMessages([
                "First complete Four IR Project CS!"
            ]));
        }

        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'integer',
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'file_path' => [
                'required',
                'string',
                'max:500',
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
            'four_ir_initiative_id' => 'required|int',
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

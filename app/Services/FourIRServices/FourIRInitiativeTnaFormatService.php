<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiativeCell;
use App\Models\FourIRInitiativeTnaFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRInitiativeTnaFormatService
 * @package App\Services
 */
class FourIRInitiativeTnaFormatService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectTnaFormatList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'];
        $workshopName = $request['workshop_name'] ?? "";
        $venue = $request['venue'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $FourIRProjectTnaFormatBuilder */
        $fourIrProjectTnaFormatBuilder = FourIRInitiativeTnaFormat::select(
            [
                'four_ir_initiative_tna_formats.id',
                'four_ir_initiative_tna_formats.four_ir_initiative_id',
                'four_ir_initiative_tna_formats.workshop_name',
                'four_ir_initiative_tna_formats.skill_required',
                'four_ir_initiative_tna_formats.start_date',
                'four_ir_initiative_tna_formats.end_date',
                'four_ir_initiative_tna_formats.venue',
                'four_ir_initiative_tna_formats.file_path',
                'four_ir_initiative_tna_formats.row_status',
                'four_ir_initiative_tna_formats.created_by',
                'four_ir_initiative_tna_formats.updated_by',
                'four_ir_initiative_tna_formats.created_at',
                'four_ir_initiative_tna_formats.updated_at'
            ]
        );

        $fourIrProjectTnaFormatBuilder->orderBy('four_ir_initiative_tna_formats.id', $order);

        if (!empty($fourIrProjectId)) {
            $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.four_ir_initiative_id', 'like', '%' . $fourIrProjectId . '%');
        }
        if (!empty($workshopName)) {
            $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.workshop_name', 'like', '%' . $workshopName . '%');
        }
        if (!empty($venue)) {
            $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.venue', 'like', '%' . $venue . '%');
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrProjectTnaFormatBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrProjectTnaFormatBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrProjects->toArray()['data'] ?? $fourIrProjects->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRInitiativeTnaFormat
     */
    public function getOneFourIrProjectTnaFormat(int $id): FourIRInitiativeTnaFormat
    {
        /** @var FourIRInitiativeTnaFormat|Builder $fourIrProjectTnaFormatBuilder */
        $fourIrProjectTnaFormatBuilder = FourIRInitiativeTnaFormat::select(
            [
                'four_ir_initiative_tna_formats.id',
                'four_ir_initiative_tna_formats.four_ir_initiative_id',
                'four_ir_initiative_tna_formats.workshop_name',
                'four_ir_initiative_tna_formats.skill_required',
                'four_ir_initiative_tna_formats.start_date',
                'four_ir_initiative_tna_formats.end_date',
                'four_ir_initiative_tna_formats.venue',
                'four_ir_initiative_tna_formats.file_path',
                'four_ir_initiative_tna_formats.row_status',
                'four_ir_initiative_tna_formats.created_by',
                'four_ir_initiative_tna_formats.updated_by',
                'four_ir_initiative_tna_formats.created_at',
                'four_ir_initiative_tna_formats.updated_at'
            ]
        );
        $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.id', '=', $id);

        return $fourIrProjectTnaFormatBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiativeTnaFormat
     */
    public function store(array $data): FourIRInitiativeTnaFormat
    {

        $fourIRProjectTnaFormat = new FourIRInitiativeTnaFormat();
        $fourIRProjectTnaFormat->fill($data);
        $fourIRProjectTnaFormat->save();
        return $fourIRProjectTnaFormat;
    }

    /**
     * @param FourIRInitiativeTnaFormat $fourIRProjectTnaFormat
     * @param array $data
     * @return FourIRInitiativeTnaFormat
     */
    public function update(FourIRInitiativeTnaFormat $fourIRProjectTnaFormat, array $data): FourIRInitiativeTnaFormat
    {
        $fourIRProjectTnaFormat->fill($data);
        $fourIRProjectTnaFormat->save();
        return $fourIRProjectTnaFormat;
    }

    /**
     * @param FourIRInitiativeTnaFormat $fourIRProjectTnaFormat
     * @return bool
     */
    public function destroy(FourIRInitiativeTnaFormat $fourIRProjectTnaFormat): bool
    {
        return $fourIRProjectTnaFormat->delete();
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
            'four_ir_initiative_id'=>[
                'required',
                'int',
                'exists:four_ir_initiatives,id,deleted_at,NULL',
                function ($attr, $value, $failed) use ($request) {
                    $mentoringTeam = FourIRInitiativeCell::where('four_ir_initiative_id', $value)->first();
                    if(empty($mentoringTeam)){
                        $failed('Complete Project Cell step first.[24000]');
                    }
                }
            ],
            'workshop_name' => [
                'required',
                'string'
            ],
            'skill_required' => [
                'required',
                'string'
            ],
             'venue' => [
               'required',
               'string'
                ],
            'start_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'file_path' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
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
            'four_ir_initiative_id'=>'required|int',
            'workshop_name' => 'nullable|string',
            'venue' => 'nullable|string',
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

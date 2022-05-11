<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRSector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FourIRSectorService

{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getSectorList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $sectorBuilder */
        $sectorBuilder = FourIRSector::select([
            'four_ir_sectors.id',
            'four_ir_sectors.title_en',
            'four_ir_sectors.title',
            'four_ir_sectors.row_status',
            'four_ir_sectors.created_by',
            'four_ir_sectors.updated_by',
            'four_ir_sectors.created_at',
            'four_ir_sectors.updated_at',
        ]);

        $sectorBuilder->orderBy('four_ir_sectors.id', $order);


        if (is_numeric($rowStatus)) {
            $sectorBuilder->where('four_ir_sectors.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $sectorBuilder->where('four_ir_sectors.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $sectorBuilder->where('four_ir_sectors.title', 'like', '%' . $title . '%');
        }
        if (!empty($author)) {
            $sectorBuilder->where('four_ir_sectors.author', 'like', '%' . $author . '%');
        }

        /** @var Collection $occupations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $occupations = $sectorBuilder->paginate($pageSize);
            $paginateData = (object)$occupations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $occupations = $sectorBuilder->get();
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
     * @return FourIRSector
     */
    public function getOneSector($id): FourIRSector
    {
        /** @var FourIRSector|Builder $sectorBuilder */
        $sectorBuilder = FourIRSector::select([
            'four_ir_sectors.id',
            'four_ir_sectors.title_en',
            'four_ir_sectors.title',
            'four_ir_sectors.row_status',
            'four_ir_sectors.created_by',
            'four_ir_sectors.updated_by',
            'four_ir_sectors.created_at',
            'four_ir_sectors.updated_at',
        ]);

        $sectorBuilder->where('four_ir_sectors.id', '=', $id);


        return $sectorBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRSector
     */
    public function store(array $data): FourIRSector
    {
        $sector = new FourIRSector();
        $sector->fill($data);
        $sector->save();
        return $sector;
    }

    /**
     * @param FourIRSector $sector
     * @param array $data
     * @return FourIRSector
     */
    public function update(FourIRSector $sector, array $data): FourIRSector
    {
        $sector->fill($data);
        $sector->save();
        return $sector;
    }

    /**
     * @param FourIRSector $sector
     * @return bool
     */
    public function destroy(FourIRSector $sector): bool
    {
        return $sector->delete();
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
                'max:400',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max:800',
                'min:2'
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
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Publication;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PublicationService
 * @package App\Services
 */
class PublicationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getPublicationList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $author = $request['author'] ?? "";
        $authorEn = $request['author_en'] ?? "";
        $IndustryAssociationId = $request['industry_association_id'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rowStatus = $request['row_status'] ?? "";



        /** @var Builder $publicationBuilder */
        $publicationBuilder = Publication::select(
            [
                'publications.id',
                'publications.title',
                'publications.title_en',
                'publications.author',
                'publications.author_en',
                'publications.title_en',
                'publications.description',
                'publications.description_en',
                'publications.industry_association_id',
                'publications.image_path',
                'publications.created_by',
                'publications.updated_by',
                'publications.created_at',
                'publications.updated_at',
                'publications.row_status'

            ]
        )->acl();
        $publicationBuilder->orderBy('publications.id', $order);

        if (!empty($titleEn)) {
            $publicationBuilder->where('publications.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $publicationBuilder->where('publications.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($rowStatus)) {
            $publicationBuilder->where('publications.row_status', $rowStatus);
        }
        if (is_numeric($IndustryAssociationId)) {
            $publicationBuilder->where('publications.industry_association_id', $IndustryAssociationId);
        }
        if (!empty($author)) {
            $publicationBuilder->where('publications.author', 'like', '%' . $author . '%');
        }
        if (!empty($authorEn)) {
            $publicationBuilder->where('publications.author_en', 'like', '%' . $authorEn . '%');
        }

        /** @var Collection $publications */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $publications = $publicationBuilder->paginate($pageSize);
            $paginateData = (object)$publications->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $publications = $publicationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $publications->toArray()['data'] ?? $publications->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOnePublication(int $id, Carbon $startTime): array
    {
        /** @var Builder $publicationBuilder */
        $publicationBuilder = Publication::select(
            [
                'publications.id',
                'publications.title',
                'publications.title_en',
                'publications.author',
                'publications.author_en',
                'publications.title_en',
                'publications.description',
                'publications.description_en',
                'publications.industry_association_id',
                'publications.image_path',
                'publications.created_by',
                'publications.updated_by',
                'publications.created_at',
                'publications.updated_at',
                'publications.row_status'
            ]
        );

        $publicationBuilder->where('publications.id', '=', $id);

        /** @var Publication $publication */
        $publication = $publicationBuilder->first();

        return [
            "data" => $publication ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return Publication
     */
    public function store(array $data): Publication
    {
        $publication = new Publication();
        $publication->fill($data);
        $publication->save();
        return $publication;
    }

    /**
     * @param Publication $publication
     * @param array $data
     * @return Publication
     */
    public function update(Publication $publication, array $data): Publication
    {
        $publication->fill($data);
        $publication->save();
        return $publication;
    }

    /**
     * @param Publication $publication
     * @return bool
     */
    public function destroy(Publication $publication): bool
    {
        return $publication->delete();
    }

    /**
     * @param Publication $publication
     * @return bool
     */
    public function restore(Publication $publication): bool
    {
        return $publication->restore();
    }


    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
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
                'max: 400',
                'min:2'
            ],
            'author' => [
                'nullable',
                'string',
                'max: 400',
                'min:2'
            ],
            'description' => [
                'required',
                'string',
            ],
            'description_en' => [
                'nullable',
                'string',
            ],
            'image_path' => [
                'nullable',
                'string',
                'max: 1000',
                'min:2'
            ],
            'industry_association_id' => [
                'required',
                'integer'
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
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
            'author' => 'nullable|max:600|min:2',
            'author_en' => 'nullable|max:600|min:2',
            'industry_association_id'=>'nullable|integer',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in(BaseModel::ROW_STATUS_ACTIVE,BaseModel::ROW_STATUS_INACTIVE),
            ],
        ], $customMessage);
    }
}

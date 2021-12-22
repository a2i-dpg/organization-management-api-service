<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\ContactUs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactUsService
 * @package App\Services
 */
class ContactUsService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getContactInfoList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $IndustryAssociationId = $request['industry_association_id'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rowStatus = $request['row_status'] ?? "";



        /** @var Builder $contactInfoBuilder */
        $contactInfoBuilder = ContactUs::select(
            [
                'contact_us.id',
                'contact_us.title',
                'contact_us.title_en',
                'contact_us.industry_association_id',
                'contact_us.country',
                'contact_us.phone_code',
                'contact_us.phone',
                'contact_us.mobile',
                'contact_us.email',
                'contact_us.created_by',
                'contact_us.updated_by',
                'contact_us.created_at',
                'contact_us.updated_at',
                'contact_us.row_status'

            ]
        )->acl();
        $contactInfoBuilder->orderBy('contact_us.id', $order);

        if (!empty($titleEn)) {
            $contactInfoBuilder->where('contact_us.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $contactInfoBuilder->where('contact_us.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($rowStatus)) {
            $contactInfoBuilder->where('contact_us.row_status', $rowStatus);
        }
        if (is_numeric($IndustryAssociationId)) {
            $contactInfoBuilder->where('contact_us.industry_association_id', $IndustryAssociationId);
        }

        /** @var Collection $contactInfoBuilder */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $contacts = $contactInfoBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $contactInfoBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $contacts->toArray()['data'] ?? $contacts->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneContactInfo(int $id, Carbon $startTime): array
    {
        /** @var Builder $contactInfoBuilder */
        $contactInfoBuilder = ContactUs::select(
            [
                'contact_us.id',
                'contact_us.title',
                'contact_us.title_en',
                'contact_us.industry_association_id',
                'contact_us.country',
                'contact_us.phone_code',
                'contact_us.phone',
                'contact_us.mobile',
                'contact_us.email',
                'contact_us.created_by',
                'contact_us.updated_by',
                'contact_us.created_at',
                'contact_us.updated_at',
                'contact_us.row_status'

            ]
        );


        $contactInfoBuilder->where('contact_us.id', '=', $id);

        /** @var ContactUs $contactInfoBuilder */
        $contact = $contactInfoBuilder->first();

        return [
            "data" => $contact ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return ContactUs
     */
    public function store(array $data): ContactUs
    {
        $contact = new ContactUs();
        $contact->fill($data);
        $contact->save();
        return $contact;
    }

    /**
     * @param ContactUs $contactUs
     * @param array $data
     * @return ContactUs
     */
    public function update(ContactUs $contactUs, array $data): ContactUs
    {
        $contactUs->fill($data);
        $contactUs->save();
        return $contactUs;
    }

    /**
     * @param ContactUs $contactUs
     * @return bool
     */
    public function destroy(ContactUs $contactUs): bool
    {
        return $contactUs->delete();
    }

    /**
     * @param ContactUs $contactUs
     * @return bool
     */
    public function restore(ContactUs $contactUs): bool
    {
        return $contactUs->restore();
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
            'industry_association_id' => [
                'required',
                'integer'
            ],
            'country' => [
                'required',
                'string',
                'max: 1000',
                'min:2'
            ],
            'phone_code' => [
                'nullable',
                'string',
                'max: 1000',
                'min:2'
            ],
            'phone' => [
                'nullable',
                'string',
                'max: 1000',
                'min:2'
            ],
            'mobile' => [
                'required',
                'string',
                'max: 1000',
                'min:2'
            ],
            'email' => [
                'required',
                'string',
                'max: 1000',
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
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
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

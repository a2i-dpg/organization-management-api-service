<?php

namespace App\Imports;

use App\Http\Controllers\Controller;
use App\Models\BaseModel;
use App\Models\Organization;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use http\Exception\RuntimeException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class OrganizationImport extends Controller implements ToCollection, WithValidation, WithHeadingRow
{
    /**
     * @throws AuthorizationException
     */
    public function prepareForValidation($data, $index)
    {
        /** @var Organization $organization */
        $organization = app(Organization::class);
        $this->authorize('create', $organization);

        $request = request()->all();
        if(!empty($request['industry_association_id'])){
            $data['industry_association_id'] = $request['industry_association_id'];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_type_id' => [
                'required',
                'int',
                'exists:organization_types,id,deleted_at,NULL'
            ],
            'sub_trade' => [
                'required',
                'integer',
                'exists:sub_trades,id,deleted_at,NULL'
            ],
            'membership_id' => [
                'required',
                'string',
            ],
            'permission_sub_group_id' => [
                'required',
                'integer'
            ],
            'date_of_establishment' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:600',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max:1200',
                'min:2'
            ],
            'loc_division_id' => [
                'required',
                'integer',
                'exists:loc_divisions,id,deleted_at,NULL'
            ],
            'loc_district_id' => [
                'required',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
                'exists:loc_upazilas,id,deleted_at,NULL'
            ],
            "location_latitude" => [
                'nullable',
                'string',
            ],
            "location_longitude" => [
                'nullable',
                'string',
            ],
            "google_map_src" => [
                'nullable',
                'integer',
            ],
            'address' => [
                'required',
                'max: 1200',
                'min:2'
            ],
            'address_en' => [
                'nullable',
                'max: 600',
                'min:2'
            ],
            "country" => [
                "nullable",
                "string",
                "min:2"
            ],
            "phone_code" => [
                "nullable",
                "string"
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'email' => [
                'required',
                'email',
                'max:320'
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 30',
            ],
            "name_of_the_office_head" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string"
            ],
            "name_of_the_office_head_designation_en" => [
                "nullable",
                "string"
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_name_en' => [
                'nullable',
                'max: 250',
                'min:2'
            ],
            'contact_person_mobile' => [
                'required',
                Rule::unique('organizations', 'contact_person_mobile')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,
            ],
            'contact_person_email' => [
                'required',
                'email',
                Rule::unique('organizations', 'contact_person_email')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'contact_person_designation' => [
                'required',
                'max: 600',
                "min:2"
            ],
            'contact_person_designation_en' => [
                'nullable',
                'max: 300',
                "min:2"
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'description_en' => [
                'nullable',
                'string',
            ],
            'domain' => [
                'nullable',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'string',
                'max:191',
                Rule::unique('organizations', 'domain')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'logo' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
    }

    /**
     * @param Collection $collection
     * @return JsonResponse
     * @throws Throwable
     */
    public function collection(Collection $collection): JsonResponse
    {
        $validated = $collection->toArray();

        /** @var Organization $organization */
        $organization = app(Organization::class);

        $validated['code'] = CodeGenerateService::getIndustryCode();

        DB::beginTransaction();
        try {

            $organization = app(OrganizationService::class)->store($organization, $validated);

            $this->organizationService->syncWithSubTrades($organization, $validated['sub_trades']);

            if (!($organization && $organization->id)) {
                throw new RuntimeException('Saving Organization/Industry to DB failed!', 500);
            }

            $validated['organization_id'] = $organization->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdRegisterUser = $this->organizationService->createUser($validated);

            Log::info('id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Organization has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {

                /** Mail send after user registration */
                $to = array($validated['contact_person_email']);
                $from = BaseModel::NISE3_FROM_EMAIL;
                $subject = "User Registration Information";
                $message = "Congratulation, You are successfully complete your registration as " . $validated['title'] . " user. Username: " . $validated['contact_person_mobile'] . " & Password: " . $validated['password'];
                $messageBody = MailService::templateView($message);
                $mailService = new MailService($to, $from, $subject, $messageBody);
                $mailService->sendMail();

                /** SMS send after user registration */
                $recipient = $validated['contact_person_mobile'];
                $smsMessage = "You are successfully complete your registration as " . $validated['title'] . " user";
                $smsService = new SmsService();
                $smsService->sendSms($recipient, $smsMessage);

                $response['data'] = $organization;
                DB::commit();
                return Response::json($response, ResponseAlias::HTTP_CREATED);
            }

            DB::rollBack();

            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            if (!empty($createdRegisterUser['_response_status']['code'])) {
                $httpStatusCode = $createdRegisterUser['_response_status']['code'];
            }

            $response['_response_status'] = [
                "success" => false,
                "code" => $httpStatusCode,
                "message" => "Error Occurred. Please Contact.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ];

            if (!empty($createdRegisterUser['errors'])) {
                $response['errors'] = $createdRegisterUser['errors'];
            }

            return Response::json($response, $httpStatusCode);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }




        foreach ($collection as $row) {
            Log::info("Value >>>>>> Value <<<<<<<<<<");
            Log::info(json_encode($row));
        }
    }
}

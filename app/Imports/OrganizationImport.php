<?php

namespace App\Imports;

use App\Facade\ServiceToServiceCall;
use App\Http\Controllers\Controller;
use App\Models\BaseModel;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\SubTrade;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\OrganizationService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class OrganizationImport implements ToCollection, WithValidation, WithHeadingRow
{
     public array $alreadyExistUsernames = [];

    /**
     * @param $data
     * @param $index
     * @return mixed
     */
    public function prepareForValidation($data, $index): mixed
    {
        $request = request()->all();
        Log::info("Data start for validation: " . json_encode($data));

        if (!empty($request['industry_association_id'])) {
            $data['industry_association_id'] = $request['industry_association_id'];
        }
        if(!empty($data['organization_type_id'])){
            $organizationType = OrganizationType::where('title', $data['organization_type_id'])->firstOrFail();
            $data['organization_type_id'] = $organizationType->id;
        }
        if (!empty($data['sub_trade'])) {
            $subTrade = SubTrade::where('title',$data['sub_trade'])->firstOrFail();
            $data['sub_trades'] = [$subTrade->id];
        }
        if(!empty($data['permission_sub_group_id'])){
            $permissionSubGroup = ServiceToServiceCall::getPermissionSubGroupsByTitle($data['permission_sub_group_id']);
            $data['permission_sub_group_id'] = $permissionSubGroup['id'];
        }
        if(!empty($data['loc_division_id'])){
            $division = LocDivision::where('title_en', $data['loc_division_id'])->firstOrFail();
            $data['loc_division_id'] = $division->id;
        }
        if(!empty($data['loc_district_id'])){
            $district = LocDistrict::where('title_en', $data['loc_district_id'])->firstOrFail();
            $data['loc_district_id'] = $district->id;
        }
        if(!empty($data['loc_upazila_id'])){
            $upazila = LocUpazila::where('title_en', $data['loc_upazila_id'])->firstOrFail();
            $data['loc_upazila_id'] = $upazila->id;
        }
        if(!empty($data['date_of_establishment'])){
            $data['date_of_establishment'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['date_of_establishment'])->format('Y-m-d');
        }
        if(!empty($data['phone_code']) && is_int($data['phone_code'])){
            $data['phone_code'] = (string)$data['phone_code'];
        }
        if(!empty($data['mobile']) && is_int($data['mobile']) && strlen((string)$data['mobile']) == 10 && explode((string)$data['mobile'], '')[0] != 0){
            $data['mobile'] = '0' . $data['mobile'];
        }
        if(!empty($data['contact_person_mobile']) && is_int($data['contact_person_mobile']) && strlen((string)$data['contact_person_mobile']) == 10 && explode((string)$data['contact_person_mobile'], '')[0] != 0){
            $data['contact_person_mobile'] = '0' . $data['contact_person_mobile'];
        }

        Log::info("The data: " . json_encode($data));

        return $data;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        Log::info("Inside rule");
        return [
            'organization_type_id' => [
                'required',
                'int',
                'exists:organization_types,id,deleted_at,NULL'
            ],
            'sub_trades' => [
                'required',
                'array',
                'min:1'
            ],
            'sub_trades.*' => [
                'nullable',
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
     * Store rouse as bulk.
     *
     * @param Collection $collection
     * @return void
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {
        Log::info("Start inside collection");

        $rows = $collection->toArray();
        Log::info("The collections are: " . json_encode($rows));
        foreach ($rows as $rowData){
            $user = ServiceToServiceCall::getUserByUsername($rowData['contact_person_mobile']);
            Log::info("Core user is: " . json_encode($user));
            if(empty($user)){
//                DB::beginTransaction();
                try {
                    $rowData['code'] = CodeGenerateService::getIndustryCode();

                    /** @var Organization $organization */
                    $organization = app(Organization::class);
                    $organization = app(OrganizationService::class)->store($organization, $rowData);

//                    Log::info("The created organization: " . json_encode($organization));
                    Log::info("The created organization demo: ");

                    app(OrganizationService::class)->syncWithSubTrades($organization, $rowData['sub_trades']);

                    if (!($organization && $organization->id)) {
                        throw new Exception('Saving Organization/Industry to DB failed!', 500);
                    }

                    $rowData['organization_id'] = $organization->id;
                    $rowData['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

                    $createdRegisterUser = app(OrganizationService::class)->createUser($rowData);

                    Log::info('id_user_info:' . json_encode($createdRegisterUser));

                    if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                        throw new Exception('Organization/Industry Creation has been failed for Contact person mobile: ' . $rowData['contact_person_mobile'], 500);
                    }

                    if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {

                        /** Mail send after user registration */
                        $to = array($rowData['contact_person_email']);
                        $from = BaseModel::NISE3_FROM_EMAIL;
                        $subject = "User Registration Information";
                        $message = "Congratulation, You are successfully complete your registration as " . $rowData['title'] . " user. Username: " . $rowData['contact_person_mobile'] . " & Password: " . $rowData['password'];
                        $messageBody = MailService::templateView($message);
                        $mailService = new MailService($to, $from, $subject, $messageBody);
                        $mailService->sendMail();
                        Log::info("Mail has been send");

                        /** SMS send after user registration */
                        $recipient = $rowData['contact_person_mobile'];
                        $smsMessage = "You are successfully complete your registration as " . $rowData['title'] . " user";
                        $smsService = new SmsService();
                        $smsService->sendSms($recipient, $smsMessage);
                        Log::info("Sms has been send here");

//                        DB::commit();
                    } else {
                        throw new Exception('Organization/Industry Creation for Contact person mobile: ' . $rowData['contact_person_mobile'] . ' not succeed!', 500);
                    }

                    Log::info("Organization for contact person mobile: " . $rowData['contact_person_mobile'] . " has been created");
                } catch (Throwable $e) {
                    Log::info("Error occurred. Inside catch block. Error is: " . json_encode($e->getMessage()));
//                    DB::rollBack();
                    throw $e;
                }
            } else {
                $this->alreadyExistUsernames[] = $rowData['contact_person_mobile'];
            }
        }

        Log::info("Successfully added all organizations");
        Log::info(json_encode($collection));
    }
}

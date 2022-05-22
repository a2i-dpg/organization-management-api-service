<?php

namespace App\Helpers\Classes;

use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceToServiceCallHandler
{

    /**
     * @param string $idpUserId
     * @return mixed
     * @throws RequestException
     */
    public function getAuthUserWithRolePermission(string $idpUserId): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'auth-user-info';
        $userPostField = [
            "idp_user_id" => $idpUserId
        ];

        $responseData = Http::withOptions([
            'verify' => config('nise3.should_ssl_verify'),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->post($url, $userPostField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        Log::info("userInfo:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param array $instituteIds
     * @return mixed
     * @throws RequestException
     */
    public function getInstituteTitleByIds(array $instituteIds): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'get-institute-title-by-ids';
        $postField = [
            "institute_ids" => $instituteIds
        ];

        $instituteData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->post($url, $postField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $instituteData;
    }

    /**
     * @param array $youthIds
     * @return mixed
     * @throws RequestException
     */
    public function getYouthProfilesByIds(array $youthIds): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/youth-profiles';
        $postField = [
            "youth_ids" => $youthIds
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->post($url, $postField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $youthData;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws RequestException
     */
    public function createEventAfterInterviewScheduleAssign(array $data): mixed
    {
        $url = clientUrl(BaseModel::CMS_CLIENT_URL_TYPE) . 'create-event-after-interview-schedule-assign';
        $responseData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->post($url, $data)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $responseData;
    }

    /**
     * @param string $permissionSubGroupTitle
     * @return mixed
     * @throws RequestException
     */
    public function getPermissionSubGroupsByTitle(string $permissionSubGroupTitle): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/permission-sub-group/' . $permissionSubGroupTitle;

        $permissionSubGroup = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $permissionSubGroup;
    }

    /**
     * @param string $userName
     * @return mixed
     * @throws RequestException
     */
    public function getCoreUserByUsername(string $userName): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/user-by-username/' . $userName;

        $user = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $user;
    }

    /**
     * @param string $userName
     * @return mixed
     * @throws RequestException
     */
    public function getYouthUserByUsername(string $userName): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/user-by-username/' . $userName;

        $user = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        return $user;
    }

    /**
     * @param string $parameter
     * @return mixed
     * @throws RequestException
     */
    public function getDomain(string $parameter): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/domain?' . $parameter;

        $domain = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('domain');
        Log::info("domain: " . $domain);
        return $domain;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function createFourIrUser(array $payload): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/create-four-ir-user';

        $userData = Http::withOptions([
            'verify' => config('nise3.should_ssl_verify'),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->post($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        Log::info("userInfo:" . json_encode($userData));

        return $userData;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function updateFourIrUser(array $payload): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/update-four-ir-user';

        $userData = Http::withOptions([
            'verify' => config('nise3.should_ssl_verify'),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->put($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        Log::info("userInfo:" . json_encode($userData));

        return $userData;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function deleteFourIrUser(array $payload): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/delete-four-ir-user';

        $userData = Http::withOptions([
            'verify' => config('nise3.should_ssl_verify'),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->delete($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');

        Log::info("userInfo:" . json_encode($userData));

        return $userData;
    }

    /**
     * @param array $filterData
     * @return mixed
     * @throws RequestException
     */
    public function getFourIrCourseList(array $filterData): mixed
    {
        $filterData['is_four_ir'] = BaseModel::BOOLEAN_TRUE;

        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-course-list';

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->get($url, $filterData)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    public function getFourIrCertificateList(array $filterData, int $fourIrInitiativeId): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-certificate-list/' . $fourIrInitiativeId;
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->get($url, $filterData)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');
    }

    public function getYouthAssessmentList(array $filterData, int $fourIrInitiativeId): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-youth-assessment-list/' . $fourIrInitiativeId;
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->get($url, $filterData)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json('data');
    }

    /**
     * @param int $courseId
     * @return mixed
     * @throws RequestException
     */
    public function getFourIrCourseByCourseId(int $courseId): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-course/' . $courseId;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function createFourIrCourse(array $payload): mixed
    {
        $payload['row_status'] = BaseModel::ROW_STATUS_INACTIVE;

        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/create-four-ir-course';

        Log::debug('createFourIrCourse: ' . $url);
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->post($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    /**
     * @param array $payload
     * @param int $courseId
     * @return mixed
     * @throws RequestException
     */
    public function updateFourIrCourse(array $payload, int $courseId): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/update-four-ir-course/' . $courseId;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->put($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    /**
     * @param int $courseId
     * @return mixed
     * @throws RequestException
     */
    public function approveFourIrCourse(int $courseId): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/approve-four-ir-course/' . $courseId;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
        ])
            ->timeout(60)
            ->put($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function getCourseEnrolledYouths(array $payload): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-course-enrolled-youths';

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws RequestException
     */
    public function getCourseBatches(array $payload): mixed
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/get-four-ir-course-batches';

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(60)
            ->get($url, $payload)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            })
            ->json();
    }
}

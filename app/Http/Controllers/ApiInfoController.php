<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiInfoController extends Controller
{
    const SERVICE_NAME = 'NISE-3 Organization Management API Service';
    const SERVICE_VERSION = 'V1';

    public function apiInfo(): JsonResponse
    {
        $response = [
            'service_name' => self::SERVICE_NAME,
            'service_version' => self::SERVICE_VERSION,
            'lumen_version' => App::version(),
            'module_list' => [
                'OrganizationType',
                'Organization',
                'RankType',
                'Rank',
                'Service',
                'Skill',
                'JobSector',
                'Occupation',
                'OrganizationUnitType',
                'OrganizationUnit',
                'organizationUnitService',
                'HumanResource',
                'HumanResourceTemplate',
            ],
            'description'=>[
                'It is a organization management api service that manages services related to organization'
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}

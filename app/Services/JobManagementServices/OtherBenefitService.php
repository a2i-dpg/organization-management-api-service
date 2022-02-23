<?php

namespace App\Services\JobManagementServices;

use App\Models\OtherBenefit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OtherBenefitService
{

    public function getOtherBenefitList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $order = $request['order'] ?? "ASC";
        /** @var Builder $otherBenefitBuilder */

        $otherBenefitBuilder = OtherBenefit::select([
            'other_benefits.id',
            'other_benefits.title',
            'other_benefits.title_en',
            'other_benefits.created_at',
            'other_benefits.updated_at',
        ]);

        $otherBenefitBuilder->orderBy('other_benefits.id', $order);

        /** @var Collection $areaOfBusiness */
        $otherBenefits = $otherBenefitBuilder->get();


        $response['order'] = $order;
        $response['data'] = $otherBenefits->toArray()['data'] ?? $otherBenefits->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;


    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'title' => 'nullable|max:300|min:2',
            'title_en' => 'nullable|max:500|min:2'
        ]);
    }

}

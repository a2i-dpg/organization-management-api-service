<?php

namespace App\Services\JobManagementServices;

use App\Models\AreaOfExperience;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AreaOfExperienceService
{
    public function getAreaOfExperienceList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $areaOfExperienceBuilder */
        $areaOfExperienceBuilder = AreaOfExperience::select([
            'area_of_experiences.id',
            'area_of_experiences.title',
            'area_of_experiences.title_en',
        ]);

        $areaOfExperienceBuilder->orderBy('area_of_experiences.id', $order);

        if (!empty($title)) {
            $areaOfExperienceBuilder->where('area_of_experiences.title', 'like', '%' . $title . '%');
        }
        if (!empty($titleEn)) {
            $areaOfExperienceBuilder->where('area_of_experiences.title_en', 'like', '%' . $titleEn . '%');
        }
        /** @var Collection $areaOfExperiences */
        $areaOfExperiences = $areaOfExperienceBuilder->get();


        $response['order'] = $order;
        $response['data'] = $areaOfExperiences->toArray()['data'] ?? $areaOfExperiences->toArray();
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
    public function filterAreaOfExperienceValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'title' => 'nullable|max:400|min:2',
            'title_en' => 'nullable|max:200|min:2'
        ]);

    }

}

<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTnaFormat;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRInitiativeTnaFormatService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeTnaFormatController extends Controller
{
    public FourIRInitiativeTnaFormatService $fourIRProjectTnaFormatService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeTnaFormatController constructor.
     *
     * @param FourIRInitiativeTnaFormatService $fourIRProjectTnaFormatService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRInitiativeTnaFormatService $fourIRProjectTnaFormatService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRProjectTnaFormatService = $fourIRProjectTnaFormatService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', FourIRInitiativeCell::class);

        $filter = $this->fourIRProjectTnaFormatService->filterValidator($request)->validate();
        $response = $this->fourIRProjectTnaFormatService->getFourIrProjectTnaFormatList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * This method can do both POST / PUT operation
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        Log::info("request data".json_encode($request->all()));
        Log::info("file request data".json_encode($request->file('workshop_method_file')));

        $validated = $this->fourIRProjectTnaFormatService->validator($request)->validate();

        $fourIrInitiative = FourIRInitiative::findOrFail($validated['four_ir_initiative_id']);

        /** Fetch the files from Request */
        $workshopFile = !empty($validated['workshop_method_workshop_numbers']) ? $request->file('workshop_method_file') : null;
        $fgdFile = !empty($validated['fgd_workshop_numbers']) ? $request->file('fgd_workshop_file') : null;
        $industryVisitFile = !empty($validated['industry_visit_workshop_numbers']) ? $request->file('industry_visit_file') : null;
        $desktopResearchFile = !empty($validated['desktop_research_workshop_numbers']) ? $request->file('desktop_research_file') : null;
        $existingReportFile = !empty($validated['existing_report_review_workshop_numbers']) ? $request->file('existing_report_review_file') : null;
        $otherWorkshopFile = !empty($validated['others_workshop_numbers']) ? $request->file('others_file') : null;

        $workshopExcelRows = null;
        $fgdExcelRows = null;
        $industryVisitExcelRows = null;
        $desktopResearchExcelRows = null;
        $existingReportExcelRows = null;
        $otherExcelRows = null;

        /** validate Excel files & store excel rows */
        if(!empty($workshopFile)){
            $workshopExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($workshopFile, FourIRInitiativeTnaFormat::WORKSHOP_TNA_METHOD);
        }
        if(!empty($fgdFile)){
            $fgdExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($fgdFile, FourIRInitiativeTnaFormat::FGD_WORKSHOP_TNA_METHOD);
        }
        if(!empty($industryVisitFile)){
            $industryVisitExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($industryVisitFile, FourIRInitiativeTnaFormat::INDUSTRY_VISIT_TNA_METHOD);
        }
        if(!empty($desktopResearchFile)){
            $desktopResearchExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($desktopResearchFile, FourIRInitiativeTnaFormat::DESKTOP_RESEARCH_TNA_METHOD);
        }
        if(!empty($existingReportFile)){
            $existingReportExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($existingReportFile, FourIRInitiativeTnaFormat::EXISTING_REPORT_VIEW_TNA_METHOD);
        }
        if(!empty($otherWorkshopFile)){
            $otherExcelRows = $this->fourIRProjectTnaFormatService->excelDataValidate($otherWorkshopFile, FourIRInitiativeTnaFormat::OTHERS_TNA_METHOD);
        }

        try {
            DB::beginTransaction();

            /** Store file_path for versioning */
            if(empty($fourIrInitiative->tna_file_path)){
                $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_TNA_STEP);
            } else {
                $this->fourIRFileLogService->updateFileLog($fourIrInitiative->tna_file_path, $validated, FourIRInitiative::FILE_LOG_TNA_STEP);
            }


            /** Save the tna_file_path & update the stepper (form_step & completion_step) information */
           $this->fourIRProjectTnaFormatService->store($fourIrInitiative, $validated);

            /** Store Or Update Excel rows with Tna Format*/
            if(!empty($workshopExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $workshopExcelRows, FourIRInitiativeTnaFormat::WORKSHOP_TNA_METHOD);
            }
            if(!empty($fgdExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $fgdExcelRows, FourIRInitiativeTnaFormat::FGD_WORKSHOP_TNA_METHOD);
            }
            if(!empty($industryVisitExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $industryVisitExcelRows, FourIRInitiativeTnaFormat::INDUSTRY_VISIT_TNA_METHOD);
            }
            if(!empty($desktopResearchExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $desktopResearchExcelRows, FourIRInitiativeTnaFormat::DESKTOP_RESEARCH_TNA_METHOD);
            }
            if(!empty($existingReportExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $existingReportExcelRows, FourIRInitiativeTnaFormat::EXISTING_REPORT_VIEW_TNA_METHOD);
            }
            if(!empty($otherExcelRows)){
                $this->fourIRProjectTnaFormatService->tnaFormatMethodStore($validated, $otherExcelRows, FourIRInitiativeTnaFormat::OTHERS_TNA_METHOD);
            }

            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative Tna Format methods added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}

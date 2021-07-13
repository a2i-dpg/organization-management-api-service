<?php


namespace App\Services;


use App\Models\JobSector;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class JobSectorService
 * @package App\Services
 */
class JobSectorService
{
    /**
     * @param Request $request
     */
    public function getJobsectorList(Request $request)
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $jobSectors = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
            ]
        )->where('job_sectors.row_status','=',JobSector::ROW_STATUS_ACTIVE)
        ->orderBy('ranks.id', $order);
        if (!empty($titleEn)) {
            $ranks->where('loc_districts.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $ranks->where('loc_districts.title_bn', 'like', '%' . $titleBn . '%');
        }



    }

}

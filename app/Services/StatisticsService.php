<?php

namespace App\Services;

use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Models\PrimaryJobInformation;


class StatisticsService
{


    public function getNiseStatistics(): array
    {
        return [
            'total_industry' => $this->getTotalIndustry(),
            'total_job_provider' => $this->getTotalIndustryAssociationWithProvidedJobs(),
            'total_industry_association' => $this->getTotalIndustryAssociation(),
            'total_popular_job' => $this->getTotalPopularJobWithJobTitle()
        ];
    }

    private function getTotalIndustry(): int
    {
        return Organization::count('id');
    }

    private function getTotalIndustryAssociation(): int
    {
        return IndustryAssociation::count('id');
    }

    private function getTotalIndustryAssociationWithProvidedJobs(): array
    {
        $builder = IndustryAssociation::query();
        $builder->select([
            "industry_associations.title as industry_associations_title",
            "industry_associations.title_en as industry_associations_title_en"
        ]);

        $builder->selectRaw('COUNT(primary_job_information.id) AS total_job_provided');
        $builder->join('primary_job_information', 'primary_job_information.industry_association_id', "industry_associations.id");
        $builder->whereNotNull('primary_job_information.published_at');
        $builder->groupBy('primary_job_information.industry_association_id');
        $builder->orderBy('total_job_provided', "DESC");

        return $builder->limit(4)->get()->toArray();
    }

    private function getTotalPopularJobWithJobTitle(): array
    {
        $builder = PrimaryJobInformation::query();
        $builder->select([
            "primary_job_information.job_title",
            "primary_job_information.job_title_en"
        ]);
        $builder->selectRaw('COUNT(applied_jobs.id) AS total_applied');
        $builder->join('applied_jobs', 'primary_job_information.job_id', "applied_jobs.job_id");
        $builder->whereNotNull('primary_job_information.published_at');
        $builder->groupBy('applied_jobs.job_id');
        $builder->orderBy('total_applied', "DESC");

        return $builder->limit(4)->get()->toArray();
    }
}

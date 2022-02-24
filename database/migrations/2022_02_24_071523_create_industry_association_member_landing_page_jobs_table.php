<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationMemberLandingPageJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_association_member_landing_page_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id', 100)->index();
            $table->unsignedInteger('industry_association_id')->index('industry_association_landing_page_indsa_id_idx');
            $table->unsignedInteger('organization_id')->index('industry_association_landing_page_org_id_idx');
            $table->unsignedTinyInteger('show_in_landing_page')->comment('1=>true,0=>false');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industry_association_member_landing_page_jobs');
    }
}

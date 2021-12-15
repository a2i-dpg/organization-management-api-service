<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_association_organization', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('industry_association_id');
            $table->unsignedInteger('organization_id');
            $table->string('membership_id', 200);

            $table->unsignedTinyInteger('row_status')
                ->default(2)
                ->comment('0 => Inactive, 1 => Approved, 2 => Pending, 3 => Rejected');

            $table->foreign('industry_association_id', 'industry_association_id')
                ->references('id')
                ->on('industry_associations')
                ->onDelete('cascade');

            $table->foreign('organization_id', 'organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industry_association_organization');
    }
}

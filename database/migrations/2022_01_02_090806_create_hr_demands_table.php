<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_demands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("industry_association_id");
            $table->unsignedInteger("organization_id");
            $table->date('end_date')->comment('Date format = Y-m-d');
            $table->unsignedMediumInteger("skill_id");
            $table->text("requirement");
            $table->text("requirement_en")->nullable();
            $table->unsignedInteger("vacancy");
            $table->unsignedInteger("remaining_vacancy")->nullable();
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('hr_demands');
    }
}

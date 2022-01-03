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
            $table->unsignedInteger("industry_id");
            $table->date('end_date')->comment('Date format = Y-m-d');
            $table->unsignedMediumInteger("skill_id");
            $table->text("requirement")->nullable();
            $table->unsignedInteger("vacancy");
            $table->unsignedInteger("remaining_vacancy")->nullable();
            $table->unsignedTinyInteger('row_status')->default(1);
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

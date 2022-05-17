<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeTotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_tots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_initiative_id');
            $table->string('organizer_name', 350);
            $table->string('organizer_email', 350);
            $table->string('organizer_address', 350);
            $table->string('organizer_address_en', 350)->nullable();
            $table->string('co_organizer_name', 350);
            $table->string('co_organizer_email', 350);
            $table->string('co_organizer_address', 350);
            $table->string('co_organizer_address_en', 350)->nullable();
            $table->date('tot_date');
            $table->string('proof_of_report_file', 350);
            $table->string('accessor_type', 100);
            $table->unsignedInteger('accessor_id');
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('four_i_r_project_tots');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_name', 500);
            $table->string('project_name_en', 300)->nullable();
            $table->string('organization_name', 500);
            $table->string('organization_name_en', 300)->nullable();
            $table->unsignedInteger('occupation_id');
            $table->text('details')->nullable();
            $table->date('start_date');
            $table->decimal('budget')->default(0);
            $table->string('project_code',20);
            $table->string('file_path');
            $table->json('tasks')->comment('1=> Roadmap Finalized, 2=>Projects reviewed by Secretary of relevant Ministries, 3=>Projects Approved');
            $table->unsignedInteger('completion_step');
            $table->unsignedInteger('form_step');
            $table->unsignedInteger('accessor_type');
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
        Schema::dropIfExists('four_ir_projects');
    }
}

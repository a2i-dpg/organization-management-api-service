<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('four_ir_initiatives', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_tagline_id');
            $table->unsignedTinyInteger('is_skill_provide')->default(0)->comment('0 => not provide, 1 => provide');
            $table->date('implementing_team_launching_date')->nullable();
            $table->date('expert_team_launching_date')->nullable();
            $table->date('cell_launching_date')->nullable();
            $table->string('tna_file_path', 300)->nullable();
            $table->string('name', 500);
            $table->string('name_en', 300)->nullable();
            $table->string('organization_name', 500);
            $table->string('organization_name_en', 300)->nullable();
            $table->decimal('budget')->default(0);
            $table->string('designation', 300);
            $table->unsignedInteger('four_ir_occupation_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('details')->nullable();
            $table->string('file_path', 300)->nullable();
            $table->json('tasks')->comment('1=> Roadmap Finalized, 2=>Projects reviewed by Secretary of relevant Ministries, 3=>Projects Approved');

            $table->unsignedInteger('completion_step');
            $table->unsignedInteger('form_step');

            $table->string('initiative_code',20);
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
        Schema::dropIfExists('four_ir_initiatives');
    }
}

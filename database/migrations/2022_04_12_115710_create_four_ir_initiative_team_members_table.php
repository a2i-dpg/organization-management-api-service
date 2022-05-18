<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeTeamMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_team_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_id');
            $table->unsignedInteger(' user_id');
            $table->string('name',200);
            $table->string('name_en',200)->nullable();
            $table->string('email',191);
            $table->string('phone_number',15);
            $table->string('designation',191);
            $table->string('organization',191);
            $table->string('file_path',191);
            $table->text('role_responsibility',200);
            $table->unsignedTinyInteger('team_type')->comment('1=> implementing team, 2=> expert team');
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
        Schema::dropIfExists('four_ir_initiative_team_members');
    }
}

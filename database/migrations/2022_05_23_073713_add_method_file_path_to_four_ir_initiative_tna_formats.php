<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMethodFilePathToFourIrInitiativeTnaFormats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('four_ir_initiative_tna_formats', function (Blueprint $table) {
            $table->string('method_file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('four_ir_initiative_tna_formats', function (Blueprint $table) {
            //
        });
    }
}

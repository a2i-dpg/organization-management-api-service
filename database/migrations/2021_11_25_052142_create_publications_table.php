<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 400);
            $table->string('title_en', 400)->nullable();
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->string('image_path', 400)->nullable();
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
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
        Schema::dropIfExists('publications');
    }
}

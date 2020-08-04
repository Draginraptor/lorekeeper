<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtImageColumnsToCharacterImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_images', function (Blueprint $table) {
            $table->string('ext_url', 200)->nullable();
            $table->renameColumn('use_cropper', 'use_custom_thumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_images', function (Blueprint $table) {
            $table->dropColumn('ext_url');
            $table->renameColumn('use_custom_thumb', 'use_cropper');
        });
    }
}

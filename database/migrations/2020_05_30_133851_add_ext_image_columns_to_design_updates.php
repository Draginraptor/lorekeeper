<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtImageColumnsToDesignUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_updates', function (Blueprint $table) {
            $table->string('ext_url', 200)->nullable();
        });
        DB::statement('ALTER TABLE design_updates CHANGE `use_cropper` `use_custom_thumb` TINYINT(1) DEFAULT 1 NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_updates', function (Blueprint $table) {
            $table->dropColumn('ext_url');
        });
        DB::statement('ALTER TABLE design_updates CHANGE `use_custom_thumb` `use_cropper` TINYINT(1) DEFAULT 1 NOT NULL;');
    }
}

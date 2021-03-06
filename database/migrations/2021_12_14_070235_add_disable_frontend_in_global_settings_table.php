<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisableFrontendInGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('disable_frontend')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['disable_frontend']);
        });
    }
}

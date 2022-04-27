<?php

use App\GlobalSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistraionOpenDisableRegistationButtonRegistrationCloseTextColumnsInGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_settings', function (Blueprint $table) {

                $table->boolean('registration_open')->default(1);
                $table->boolean('registration_disable_button')->default(1);
                 $table->text('registraion_message')->nullable();
                                        
        });
        $message = GlobalSetting::first();
        $message->registraion_message = 'Registration is currently closed. Please try again later. If you have any inquiries feel free to contact us';
        $message->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('globl_settings', function (Blueprint $table) {
            //
        });
    }
}

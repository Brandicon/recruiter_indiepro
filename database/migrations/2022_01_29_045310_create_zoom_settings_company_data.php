<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\ZoomSetting;

class CreateZoomSettingsCompanyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies =  \App\Company::withoutGlobalScope('active')->get();
        foreach ($companies as $company) {
            $setting = ZoomSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->first();
            if(is_null($setting)){
                $setting = new ZoomSetting();
                $setting->company_id = $company->id;
                $setting->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('zoom_settings');
    }
}

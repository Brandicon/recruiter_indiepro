<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\ThemeSetting;
use App\GlobalSetting;
use Illuminate\Http\Request;
use App\Http\Requests\CustomUrl;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\SuperAdminBaseController;

class SuperAdminThemeSettingsController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.themeSettings';
        $this->pageIcon = 'ti-settings';
    }

    public function index(){
        $this->global = GlobalSetting::first();
        return view('super-admin.theme-settings.index', $this->data);
    }

    public function store(CustomUrl $request){
        $theme = ThemeSetting::whereNull('company_id')->first();
        $theme->primary_color = $request->primary_color;
        $theme->front_custom_css =  $request->front_custom_css;
        $theme->admin_custom_css =  $request->admin_custom_css;
        $theme->save();

        $this->customUrl($request);

        return Reply::redirect(route('superadmin.theme-settings.index'), __('menu.themeSettings').' '.__('messages.updatedSuccessfully'));
    }
    public function rtlTheme(Request $request)
    {
        $setting = ThemeSetting::whereNull('company_id')->first();
        $setting->rtl = $request->rtl ==  'true' ? 1 : 0;
        $setting->save();
        session()->forget('company_setting');
       return Reply::redirect(route('superadmin.theme-settings.index'), __('messages.updatedSuccessfully'));
    }

    public function disableFrontend(Request $request)
    {
        $setting = GlobalSetting::where('id', '=', $this->global->id)->first();
        $setting->disable_frontend = $request->disable_frontend;
        $setting->save();
    }

    public function customUrl( $request){

        $setting = GlobalSetting::first();
        $setting->setup_homepage = $request->setup_homepage;
        $setting->custom_homepage_url = $request->custom_homepage_url;
        $setting->save();
    }

}

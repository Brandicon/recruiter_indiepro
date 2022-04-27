<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\ThemeSetting;
use App\GlobalSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\SuperAdminBaseController;

class SuperAdminSignupSettingsController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.signupSetting';
        $this->pageIcon = 'ti-settings';
    }

    public function index(){
        $this->registration = GlobalSetting::first();
      //dd($this->registration);
        return view('super-admin.signup-settings.index', $this->data);
    }
   
    public function update(Request $request){

        $globalSetting = GlobalSetting::first();
        $globalSetting->registration_open =  $request->has('status') ? $request->status: 0;
        $globalSetting->registraion_message = $request->has('message') ? $request->message : $globalSetting->registraion_message;
        $globalSetting->registration_disable_button =  $request->has('disable') ? $request->disable : 0;
        $globalSetting->save();
        
        return Reply::success(__('messages.noteUpdateSuccess'));
    }
           
}


        
        

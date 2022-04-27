<?php

namespace App\Http\Controllers\Auth;

use App\FrontCmsHeader;
use App\Http\Controllers\Controller;
use App\ThemeSetting;
use App\User;
use App\Company;
use App\RoleUser;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\GlobalSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';
    
    private $indieApiUser = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');

        App::setLocale($this->global->locale);
    }

    public function showLoginForm()
    {
        if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }

        if(auth()->check()) {
            if(auth()->user()->is_superadmin) {
                return redirect(route('superadmin.dashboard.index'));
            }
            return redirect('admin/dashboard');
        }

        $setting = $this->global;
        $global = $this->global;
        $frontTheme = ThemeSetting::whereNull('company_id')->first();
        // $disableButton = GlobalSetting::first();
        $headerData = FrontCmsHeader::first();

        return view('auth.login', [
            'setting' => $setting,
            'frontTheme' => $frontTheme,
            'headerData' => $headerData,
            'global' => $global,
            'adminTheme' => $this->adminTheme
        ]);
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
        //return $request->only($this->username(), 'password');
        return [
            'email' => $request->{$this->username()},
            'password' => $request->password,
            'status' => 'active'
        ];
    }

    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ];
        
        
         
        $response = Http::post('https://indiepro.io/api/checkRegistered', [
            'email' => $request->email
        ])->json();
        
        $indieApiUser = [];
        if($response['status'] == true){
            $indieApiUser = $response['data']['user'];
        }
        
    
        if(empty($indieApiUser) && count($indieApiUser) == 0){
            // User type from email/username
            $user = User::where($this->username(), $request->{$this->username()})->first();
            
        }else{
                $user = User::where('email', $indieApiUser['email'])->first();
            if($user){
                $user->name = $indieApiUser['name'];
                $user->email = $indieApiUser['email'];
                $user->password = $indieApiUser['password'];
                $user->image = $indieApiUser['image'];
                $user->mobile = $indieApiUser['mobile'];
                $user->updated_at = \Carbon\Carbon::now();
                $user->status = $indieApiUser['status'];
                $user->save();
                
                $company = Company::where('company_email',$indieApiUser['company']['company_email'])->first();
                
                $company->company_name = $indieApiUser['company']['company_name'];
                $company->company_email = $indieApiUser['company']['company_email'];
                $company->company_phone= $indieApiUser['company']['company_phone'];
                $company->logo= $indieApiUser['company']['logo_url'];
                $company->website= $indieApiUser['company']['website'];
                $company->address= $indieApiUser['company']['address'];
                $company->locale= $indieApiUser['company']['locale'];
                $company->timezone= $indieApiUser['company']['timezone'];
                $company->status= $indieApiUser['company']['status'];
                $company->updated_at = \Carbon\Carbon::now();
                $company->package_type= $indieApiUser['company']['package_type'];
                $company->licence_expire_on= $indieApiUser['company']['licence_expire_on'];
                $company->trial_ends_at= $indieApiUser['company']['trial_ends_at'];
                $company->login_background = $indieApiUser['company']['login_background_url'];
                $company->save();
            }else{
                $company = Company::create([
                    "package_id"=> 3,
                    "company_name" => $indieApiUser['company']['company_name'] ?? null,
                    "company_email" => $indieApiUser['company']['company_email'] ?? null,
                    "company_phone"=> $indieApiUser['company']['company_phone'] ?? null,
                    "logo"=> $indieApiUser['company']['logo_url'] ?? null,
                    "website"=> $indieApiUser['company']['website'] ?? null,
                    "address"=> $indieApiUser['company']['address'] ?? null,
                    "locale"=> $indieApiUser['company']['locale'] ?? null,
                    "timezone"=> $indieApiUser['company']['timezone'] ?? null,
                    "status"=> $indieApiUser['company']['status'] ?? null,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    "package_type"=> $indieApiUser['company']['package_type'] ?? null,
                    "licence_expire_on"=> $indieApiUser['company']['licence_expire_on'] ?? null,
                    "job_opening_title"=> "Find and hire talents in games, film, media & entertainment.",
                    "job_opening_text"=> "Indiepro Recruiter is the industry's FREE talent r...",
                    "stripe_id"=> null,
                    "card_brand"=> null,
                    "card_last_four"=> null,
                    "trial_ends_at"=> $indieApiUser['company']['trial_ends_at'] ?? null,
                    "login_background"=> $indieApiUser['company']['login_background_url'] ?? null,
                ]);
                
                $user = User::create([
                            'name' => $indieApiUser['name'],
                            'email' => $indieApiUser['email'],
                            'password' => $indieApiUser['password'],
                            'image' => $indieApiUser['image'],
                            'company_id' => $company->id,
                            'mobile' => $indieApiUser['mobile'],
                            'created_at' => \Carbon\Carbon::now(),
                            'updated_at' => \Carbon\Carbon::now(),
                            'is_superadmin' => 1,
                            'status' => $indieApiUser['status'],
                        ]);
                        
                $role = new RoleUser();
                $role->user_id = $user->id;
                $role->role_id = 1;
                $role->save();
                
            }
                

        }

        if (module_enabled('Subdomain')) {
            $rules = $this->rulesValidate($user);
        }
        $this->validate($request, $rules);
    }
    protected function redirectTo()
    {
        $user = auth()->user();
        if($user->is_superadmin) {
            return 'super-admin/dashboard';
        }
        return 'admin/dashboard';
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $this->guard()->logout();

        $request->session()->invalidate();

        if (module_enabled('Subdomain')) {
            if ($user->is_superadmin) {
                return $this->loggedOut($request) ?: redirect(route('front.super-admin-login'));
            }
            return redirect(route('login'));
        }

        return redirect(route('login'));
    }

    private function rulesValidate($user){
        if (Str::contains(url()->previous(),'super-admin-login')) {
            $rules = [
                $this->username() => [
                    'required',
                    'string',
                    Rule::exists('users', 'email')->where(function ($query) {
                        $query->where('is_superadmin', '1');
                    })
                ],
                'password' => 'required|string',
            ];
        }else{
            $company = getCompanyBySubDomain();

            $rules = [
                $this->username() => [
                    'required',
                    'string',
                    Rule::exists('users', 'email')->where(function ($query) use ($company) {
                        $query->where('company_id', $company->id);
                    })
                ],
                'password' => 'required|string',

            ];
        }
        return $rules;
    }

    private function get_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if ($count > 2) {
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }
    
    public function loginViaAPI(Request $request)
    {
        $response = Http::post('https://indiepro.io/api/checkRegistered', [
            'email' => $request->email
        ])->json();
        
        $indieApiUser = [];
        if($response['status'] == true){
            $indieApiUser = $response['data']['user'];
        }
        
    
        if(empty($indieApiUser) && count($indieApiUser) == 0){
            // User type from email/username
            $user = User::where($this->username(), $request->{$this->username()})->first();
            
        }else{
            $user = User::where('email', $indieApiUser['email'])->first();
            if($user){
                $user->name = $indieApiUser['name'];
                $user->email = $indieApiUser['email'];
                $user->password = $indieApiUser['password'];
                $user->image = $indieApiUser['image'];
                $user->mobile = $indieApiUser['mobile'];
                $user->updated_at = \Carbon\Carbon::now();
                $user->status = $indieApiUser['status'];
                $user->save();
                
                $company = Company::where('company_email',$indieApiUser['company']['company_email'])->first();
                
                $company->company_name = $indieApiUser['company']['company_name'];
                $company->company_email = $indieApiUser['company']['company_email'];
                $company->company_phone= $indieApiUser['company']['company_phone'];
                $company->logo= $indieApiUser['company']['logo_url'];
                $company->website= $indieApiUser['company']['website'];
                $company->address= $indieApiUser['company']['address'];
                $company->locale= $indieApiUser['company']['locale'];
                $company->timezone= $indieApiUser['company']['timezone'];
                $company->status= $indieApiUser['company']['status'];
                $company->updated_at = \Carbon\Carbon::now();
                $company->package_type= $indieApiUser['company']['package_type'];
                $company->licence_expire_on= $indieApiUser['company']['licence_expire_on'];
                $company->trial_ends_at= $indieApiUser['company']['trial_ends_at'];
                $company->login_background = $indieApiUser['company']['login_background_url'];
                $company->save();
            }else{
                $company = Company::create([
                    "package_id"=> 3,
                    "company_name" => $indieApiUser['company']['company_name'] ?? null,
                    "company_email" => $indieApiUser['company']['company_email'] ?? null,
                    "company_phone"=> $indieApiUser['company']['company_phone'] ?? null,
                    "logo"=> $indieApiUser['company']['logo_url'] ?? null,
                    "website"=> $indieApiUser['company']['website'] ?? null,
                    "address"=> $indieApiUser['company']['address'] ?? null,
                    "locale"=> $indieApiUser['company']['locale'] ?? null,
                    "timezone"=> $indieApiUser['company']['timezone'] ?? null,
                    "status"=> $indieApiUser['company']['status'] ?? null,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    "package_type"=> $indieApiUser['company']['package_type'] ?? null,
                    "licence_expire_on"=> $indieApiUser['company']['licence_expire_on'] ?? null,
                    "job_opening_title"=> "Find and hire talents in games, film, media & entertainment.",
                    "job_opening_text"=> "Indiepro Recruiter is the industry's FREE talent r...",
                    "stripe_id"=> null,
                    "card_brand"=> null,
                    "card_last_four"=> null,
                    "trial_ends_at"=> $indieApiUser['company']['trial_ends_at'] ?? null,
                    "login_background"=> $indieApiUser['company']['login_background_url'] ?? null,
                ]);
                
                $user = User::create([
                            'name' => $indieApiUser['name'],
                            'email' => $indieApiUser['email'],
                            'password' => $indieApiUser['password'],
                            'image' => $indieApiUser['image'],
                            'company_id' => $company->id,
                            'mobile' => $indieApiUser['mobile'],
                            'created_at' => \Carbon\Carbon::now(),
                            'updated_at' => \Carbon\Carbon::now(),
                            'is_superadmin' => 1,
                            'status' => $indieApiUser['status'],
                        ]);
                        
                $role = new RoleUser();
                $role->user_id = $user->id;
                $role->role_id = 1;
                $role->save();
                
            }
        }
        
        if($request->type == 'dashboard'){
            $url = route('admin.dashboard');
        }elseif($request->type == 'reports'){
            $url = route('admin.report.index');
        }else{
            $url = route('admin.dashboard');
        }
        
        return response()->json( ["url" => $url]);
    }
}

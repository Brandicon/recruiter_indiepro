<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Company;
use Carbon\Carbon;
use App\StickyNote;
use App\ZoomSetting;
use App\ThemeSetting;
use App\GlobalSetting;
use App\CompanyPackage;
use App\LanguageSetting;
use App\LinkedInSetting;
use App\PermissionRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\FileSystemSettingTrait;
use Illuminate\Support\Facades\Http;


class AdminBaseController extends Controller
{
    use FileSystemSettingTrait;
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Inject currently logged in user object into every view of user dashboard
        
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            if ($this->user && $this->user->roles->count() > 0) {
                $this->todoItems = $this->user->todoItems()->groupBy('status', 'position')->get();
            }
            $this->superSettings = GlobalSetting::with('currency')->first();
        
            $userPermissions = array();
            
            if(auth()->user()->is_superadmin == 1){
                
                $this->getPermissions = User::with('roles.permissions.permission')->find($this->user->id);
                 
                foreach ($this->getPermissions->roles[0]->permissions as $key => $value) {
                    $userPermissions[] = $value->permission->name;
                }
            }else{
                $this->getPermissions = PermissionRole::where('role_id',auth()->user()->role->role_id)->get();
                 foreach ($this->getPermissions as $key => $value) {
                    $userPermissions[] = $value->permission->name;
                }
            }
            
            //It will call Main Api on Page reload
            
            if(auth()->check()){
                $response = Http::post('https://indiepro.io/api/checkRegistered', [
                    'email' => auth()->user()->email
                ])->json();

                $indieApiUser = [];
                if($response['status'] == true){
                    $indieApiUser = $response['data']['user'];
                    $user = User::where('email', $indieApiUser['email'])->first();
                    if($user){
                        $user->name = $indieApiUser['name'];
                        $user->email = $indieApiUser['email'];
                        $user->password = $indieApiUser['password'];
                        $user->image = $indieApiUser['image_url'];
                        $user->company_id = $indieApiUser['company_id'];
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
                    }
                }
            }
           
           
            $this->userPermissions = $userPermissions;

            $this->global = Company::findOrFail($this->user->company_id);
            $this->adminTheme = ThemeSetting::where('company_id', '=', $this->global->id)->first();
            $this->companyName = $this->global->company_name;
            $this->rtl = $this->adminTheme->rtl;
            $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();
            $this->activePackage = CompanyPackage::with('package')->where('company_id', $this->user->company_id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where(DB::raw('DATE(end_date)'), '>=', DB::raw('CURDATE()'));
                    $query->orWhereNull('end_date');
                })

                ->first();
            $this->setFileSystemConfigs();
            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME, $this->global->locale . '_' . strtoupper($this->global->locale));
            $this->linkedinGlobal = LinkedInSetting::first();
            $this->zoom_setting = ZoomSetting::first();

            $this->stickyNotes = StickyNote::where('user_id', $this->user->id)
                ->orderBy('updated_at', 'desc')
                ->get();
            return $next($request);
        });
    }

    public function generateTodoView()
    {
        $pendingTodos = $this->user->todoItems()->status('pending')->orderBy('position', 'DESC')->limit(5)->get();
        $completedTodos = $this->user->todoItems()->status('completed')->orderBy('position', 'DESC')->limit(5)->get();

        $view = view('sections.todo_items_list', compact('pendingTodos', 'completedTodos'))->render();

        return $view;
    }
}

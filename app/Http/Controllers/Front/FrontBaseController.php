<?php

namespace App\Http\Controllers\Front;

use App\Company;
use Carbon\Carbon;
use App\ThemeSetting;
use App\LanguageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\FileSystemSettingTrait;
use Illuminate\Support\Facades\Cookie;

class FrontBaseController extends Controller
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
        return isset($this->data[ $name ]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Inject currently logged in user object into every view of user dashboard

        $this->frontTheme = ThemeSetting::first();
        $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name', 'asc')->get();

        $this->setFileSystemConfigs();
        if (Cookie::get('language_code')) {
//            $langArray = explode('|', decrypt(Cookie::get('language_code'), false));
            $this->global->locale = Cookie::get('language_code');
            App::setLocale($this->global->locale);
        } else {
            App::setLocale($this->global->locale);
        }

    }
}

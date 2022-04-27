<?php

namespace App\Http\Controllers\SuperAdmin;

use App\GlobalSetting;
use App\Http\Requests\StorageSetting as RequestsStorageSetting;
use App\StorageSetting;
use Illuminate\Http\Request;
use Froiden\Envato\Helpers\Reply;
use Illuminate\Support\Facades\DB;

class StorageController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.storageSetting';
        $this->pageIcon = 'icon-settings';
    }

    public function index() {

        $Data = StorageSetting::all();
        $this->local = $Data->filter(function ($value, $key) {
            return $value->filesystem == 'local' ;
        })->first();
        $this->S3data = $Data->filter(function ($value, $key) {
            return $value->filesystem == 'aws' ;
        })->first();


        if (!is_null($this->S3data)) {
            $authKeys = json_decode($this->S3data->auth_keys);
            $this->S3data->driver = $authKeys->driver;
            $this->S3data->key = $authKeys->key;
            $this->S3data->secret = $authKeys->secret;
            $this->S3data->region = $authKeys->region;
            $this->S3data->bucket = $authKeys->bucket;
        }

        return view('super-admin.Storage-setting.index', $this->data);
    }

       public function store(RequestsStorageSetting $request) {
        if($request->storage == 'local'){

            $storage = StorageSetting::where('filesystem', 'local')->first();
            $storage->filesystem = $request->storage;
            $storage->status = 'enabled';
            $storage->save();
        }

        if($request->storage == 'aws'){
            
            $storage = StorageSetting::where('filesystem', 'aws')->first();

            if(is_null($storage)){
                $storage = new StorageSetting(); 
            }
              
            $storage->filesystem = $request->storage;
            $data = '{"driver": "s3", "key": "' . $request->aws_key . '", "secret": "' . $request->aws_secret . '", "region": "' . $request->aws_region . '", "bucket": "' . $request->aws_bucket . '"}';
            $storage->auth_keys = $data;
            $storage->status = 'enabled';
            $storage->save();
        }
     

        StorageSetting::where('filesystem', '!=' ,$request->storage)->update(['status' => 'disabled']);
        
        return Reply::success(__('messages.updateSuccess'));
    }

}

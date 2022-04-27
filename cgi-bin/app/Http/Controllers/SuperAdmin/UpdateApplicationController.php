<?php

namespace App\Http\Controllers\SuperAdmin;
use GuzzleHttp\Client;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZanySoft\Zip\Zip;
use Illuminate\Support\Facades\File;


class UpdateApplicationController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.updateApplication';
        $this->pageIcon = __('ti-settings');
    }

    public function index()
    {
        try {
            $results = DB::select(DB::raw('select version()'));
            $this->mysql_version = $results[0]->{'version()'};
            $this->databaseType = 'MySQL Version';

            if (strpos($this->mysql_version, 'Maria') !== false) {
                $this->databaseType = 'Maria Version';
            }
        }catch (\Exception $e) {
            $this->mysql_version = null;
        }
        $this->reviewed = file_exists(storage_path('reviewed'));
        return view('super-admin.update-application.index', $this->data);
    }

    public function store(Request $request)
    {

        config(['filesystems.default' => 'storage']);
        $path = storage_path('app') . '/Modules/' . $request->file->getClientOriginalName();
        if (file_exists($path)) {
            File::delete($path);
        }

        $request->file->storeAs('/', $request->file->getClientOriginalName());
    }

    public function deleteFile(Request $request)
    {
        $filePath = $request->filePath;
        File::delete($filePath);
        return Reply::success(__('messages.fileDeleted'));
    }
}

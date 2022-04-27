<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Company;
use App\Job;
use App\JobCompany;

class AddJobMetaInJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies =  Company::all();
        foreach($companies as $company)
        {
            $jobs =  Job::withoutGlobalScope('company')->where('company_id',$company->id)->get();

            if($jobs){
                foreach($jobs as $job){
                    if(is_null($job->meta_details)){
                        $job->meta_details = [
                            'title' => $job->title,
                            'description' => $job->job_description,
                        ];
                    }
                    if(is_null($job->required_columns)){
                        $job->required_columns = [
                            'gender' => false,
                            'dob' => false,
                            'country' => false
                        ];
                    }
                    if(is_null($job->section_visibility)){
                        $job->section_visibility = [
                            'profile_image' => 'yes',
                            'resume' => 'yes',
                            'cover_letter' => 'yes',
                            'terms_and_conditions' => 'yes'
                        ];
                    }
                    $job->save();
                }
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
        //
    }
}
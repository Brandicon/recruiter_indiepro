<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Company;
use App\Job;
use App\JobCompany;

class AddCompanyIdJobCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies =  Company::withCount('jobCompany')->get();
        foreach($companies as $company)
        {
            $jobs =  Job::with('jobCompany')->withoutGlobalScope('company')->where('company_id',$company->id)->whereNotNull('job_company_id')->get();

            if($jobs){
                foreach($jobs as $job){
                    if(is_null($job->jobCompany->company_id)){
                        $jobCompany = new JobCompany();
                        $jobCompany->company_id = $company->id;
                        $jobCompany->company_name = $job->jobCompany->company_name;
                        $jobCompany->save();

                      Job::withoutGlobalScope('company')->where('company_id',$company->id)->where('job_company_id', $job->job_company_id)->update(['job_company_id' => $jobCompany->id]);
                    }
                }
            }
        }

        foreach($companies as $company)
        {

            if(is_null($company->job_company_count) || $company->job_company_count == 0){
                $jobCompany = new JobCompany();
                $jobCompany->company_id = $company->id;
                $jobCompany->company_name = $company->company_name;
                $jobCompany->save();

                Job::withoutGlobalScope('company')->where('company_id', $company->id)->whereNull('job_company_id')->update(['job_company_id' => $jobCompany->id]);
            }

        }

        JobCompany::withCount('jobs')->whereNull('company_id')->delete();
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
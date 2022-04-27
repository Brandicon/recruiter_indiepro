<?php

namespace App\Observers;

use App\JobCompany;

class jobCompanyObserver
{
    public function saving(JobCompany $jobCompany)
    {
        if (company()) {
            $jobCompany->company_id = company()->id;
        }
    }
    public function updating(JobCompany $jobCompany)
    {
        if (company()) {
            $jobCompany->company_id = company()->id;
        }
    }
}

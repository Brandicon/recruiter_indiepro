<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JobCompany extends Model
{
    protected $fillable = ['company_name', 'company_email', 'company_phone', 'website', 'address', 'show_in_frontend','status','logo'];
    protected $appends = [
        'logo_url'
    ];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('job_companies.company_id', user()->company_id);
            }
        });
    }
    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('logo-not-found.png');
        }
        
        return asset_url_local_s3('company-logo/' . $this->logo);
    }
    public function jobs()
    {
        return $this->hasMany(Job::class, 'id');
    }
}

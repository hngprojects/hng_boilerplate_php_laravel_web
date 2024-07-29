<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'title', 'description', 'location', 'salary', 
        'deadline','company_name', 'work_mode', 'job_type', 'experience_level',
        'organisation_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_users', 'job_id', 'user_id')->using(JobUser::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'location',
        'salary',
        'job_type',
        'experience_level',
        'work_mode',
        'benefits',
        'deadline',
        'key_responsibilities',
        'qualifications',
        'user_id'
    ];
    

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_users', 'job_id', 'user_id')->using(JobUser::class);
    }
}

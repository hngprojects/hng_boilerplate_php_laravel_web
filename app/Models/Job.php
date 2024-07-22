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
        'job_type',
        'salary',
        'company_name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_user', 'job_id', 'user_id')->using(JobUser::class);
    }
}

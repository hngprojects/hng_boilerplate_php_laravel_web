<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'template',
        'status',
    ];

    public function emailRequests()
    {
        return $this->hasMany(EmailRequest::class, 'template_id');
    }
}

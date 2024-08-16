<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'template',
        'status',
    ];

    protected static function boot()
{
    parent::boot();

    static::saving(function ($model) {
        $model->title = Str::slug($model->title);
    });
}

    public function emailRequests()
    {
        return $this->hasMany(EmailRequest::class, 'template_id');
    }
}

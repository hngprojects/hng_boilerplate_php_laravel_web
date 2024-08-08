<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailRequest extends Model
{
    use HasFactory;
    
    protected $table = 'email_requests';
    protected $keyType = 'uuid';
    public $incrementing = false;
    protected $fillable = [
        "template_id",
        "subject",
        "recipient",
        "variables",
        "status"
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }
}

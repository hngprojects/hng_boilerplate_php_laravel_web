<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuestMessage;
class Quest extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status'];

    public function messages()
    {
        return $this->hasMany(QuestMessage::class)->orderBy('sequence');
    }
}

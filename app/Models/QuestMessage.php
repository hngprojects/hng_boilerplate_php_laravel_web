<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestMessage extends Model
{
    use HasFactory;

    protected $fillable = ['quest_id', 'key', 'question', 'answer', 'sequence'];

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}

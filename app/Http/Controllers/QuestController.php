<?php

namespace App\Http\Controllers;

use App\Events\QuestUpdated;
use App\Models\Quest;
use App\Models\QuestMessage;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function getQuestMessages($id)
    {
        $quest = Quest::find($id);
        if (!$quest) {
            return response()->json(['message' => 'Quest not found'], 404);
        }

        $messages = $quest->messages;
        return response()->json($messages);
    }
}

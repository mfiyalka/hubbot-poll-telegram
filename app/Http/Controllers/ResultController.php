<?php

namespace App\Http\Controllers;

use App\Entities\Answer;
use App\Entities\Poll;
use Auth;

class ResultController extends Controller
{
    public function index($id)
    {
        if (!$poll = Poll::find($id)) {
            abort(404);
        }

        if (Auth::user()->id != $poll->customer->identifier) {
            abort(404);
        }

        $answers = Answer::wherePollId($id)->paginate(25);
        return view('result', compact('poll', 'answers'));
    }
}

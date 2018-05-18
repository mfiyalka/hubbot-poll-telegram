<?php

namespace App\Http\Controllers;

use App\Entities\Poll;
use Auth;

class PollController extends Controller
{
    public function index()
    {
        $identifier = Auth::user()->customer->id;
        $polls = Poll::where(['identifier' => $identifier])->paginate(25);
        return view('polls', compact('polls'));
    }
}

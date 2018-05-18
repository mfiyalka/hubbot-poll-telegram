<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;

class AuthController  extends Controller
{
    /**
     * @var TelegramLoginAuth
     */
    protected $telegram;

    /**
     * AuthController constructor.
     *
     * @param TelegramLoginAuth $telegram
     */
    public function __construct(TelegramLoginAuth $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Get user info and log in (hypothetically)
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleTelegramCallback()
    {
        if ($this->telegram->validate()) {
            $userTelegram = $this->telegram->user();

            if (is_null($user = User::find($userTelegram['id']))) {
                $user = User::create($userTelegram);
            }

            Auth::login($user);
        }

        return redirect('/');
    }
}

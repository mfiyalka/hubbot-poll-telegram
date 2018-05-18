<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Entities\Customer;
use App\Entities\Poll;
use Telegram\Bot\Keyboard\Keyboard;

class PollFlow extends Flow
{
    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    public function run()
    {
        $customer_id = Customer::whereIdentifier($this->user->id)->first()->id;
        $polls = Poll::whereIdentifier($customer_id)->take(10)->orderBy('created_at', 'desc')->get();
        $render = view('telegram.polls', compact('polls'))->render();
        $this->reply($render);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    public function questions()
    {
        $command = $this->update->getMessage()->text;
        $poll_id = preg_replace("/[^0-9]/", '', $command);
        $customer_id = Customer::whereIdentifier($this->user->id)->first()->id;
        $poll = Poll::where(['id' => $poll_id, 'identifier' => $customer_id])->first();

        if (is_null($poll)) {
            $this->reply('Poll not found');
            return;
        }

        $render = view('telegram.questions', compact('poll'))->render();
        $this->reply($render);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function results()
    {
        $command = $this->update->getMessage()->text;
        $poll_id = preg_replace("/[^0-9]/", '', $command);

        $keyboard = Keyboard::make()
            ->inline()
            ->row(Keyboard::inlineButton([
                'text' => 'Show results',
                'url' => url("/result/{$poll_id}")
            ]));

        $this->reply('Results', $keyboard);
    }
}

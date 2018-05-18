<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Entities\Customer;
use App\Entities\Poll;
use Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class AnswerInlineQueryFlow extends Flow
{
    /**
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function run()
    {
        $data = $this->update->inlineQuery;
        $query = $data->query;

        if (mb_strlen($query, 'UTF-8') < 2) {
            $polls = Poll::where([
                'identifier' => Customer::whereIdentifier($this->user->id)->first()->id
            ])->take(50)->orderBy('created_at', 'desc')->get();
        } else {
            $polls = Poll::where([
                'identifier' => Customer::whereIdentifier($this->user->id)->first()->id,
                'title' => $query,
            ])->take(50)->orderBy('created_at', 'desc')->get();
        }

        $results = [];
        /** @var Poll $poll */
        foreach ($polls as $key => $poll) {
            $description_short = '';
            $description_full = '';
            foreach (unserialize($poll->questions) as $item => $question) {
                $number = $item + 1;
                $description_short = $description_short . "{$number}. $question ";
                $description_full = $description_full . "{$number}. $question ".PHP_EOL;
            }

            $username = config('telegram.bots.mybot.username');
            $keyboard = Keyboard::make()
                ->inline()
                ->row(Keyboard::inlineButton([
                    'text' => 'Poll',
                    'url' => "t.me/{$username}?start=p{$poll->id}"
                ]));

            $results[] = [
                'type' => 'article',
                'id' => (string) $poll->id,
                'title' => $poll->title,
                'description' => $description_short,
                'reply_markup' => $keyboard,
                'input_message_content' => [
                    'message_text' => "<b>{$poll->title}</b>".PHP_EOL.PHP_EOL.$description_full,
                    'parse_mode' => 'HTML'
                ]
            ];
        }

        Telegram::bot()->answerInlineQuery([
            'inline_query_id' => $this->update->inlineQuery->id,
            'results' => json_encode($results),
            'cache_time' => 5
        ]);
    }
}

<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Entities\Customer;
use App\Entities\Poll;
use Telegram\Bot\Keyboard\Keyboard;

class NewPollFlow extends Flow
{
    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function start()
    {
        $this->reply('Let\'s create a new poll. First, send me the title of poll.');
        $this->setContext($this, 'firstQuestion');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function firstQuestion()
    {
        $title = $this->update->getMessage()->text;
        $this->userStorage()->save(['last_poll' => ['title' => $title]]);
        $message = "Creating a new poll: '{$title}'".PHP_EOL.PHP_EOL.
            "Please send me the first question.";
        $this->reply($message);
        $this->setContext($this, 'anotherQuestion');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function anotherQuestion()
    {
        $question = $this->update->getMessage()->text;

        $storage = $this->userStorage()->get('last_poll');
        $this->userStorage()->save($storage);
        $storage['questions'][] = $question;

        $this->userStorage()->save(['last_poll' => $storage]);
        $message = "Good. Now send me another question.".PHP_EOL.PHP_EOL.
            "When you've added enough questions, simply send /done to publish the poll.";
        $this->reply($message);
        $this->setContext($this, 'anotherQuestion');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function done()
    {
        $storage = $this->userStorage()->get('last_poll');
        $title = $storage['title'];

        $questions = '';
        foreach ($storage['questions'] as $key => $poll) {
            $number = $key + 1;
            $questions = $questions . "$number. " . $poll . PHP_EOL;
        }

        $username = config('telegram.bots.mybot.username');
        $message = "👍 Poll created. You can now publish it to a group or send it to your friends in a private message. To do this, tap the button below or start your message in any other chat with @{$username} and select one of your polls to send.";
        $this->reply($message);

        $keyboard = Keyboard::make()
            ->inline()
            ->row(Keyboard::inlineButton([
                'text' => 'Publish poll',
                'switch_inline_query' => $title
            ]));
        $message = "<b>{$title}</b>".PHP_EOL.PHP_EOL.$questions;
        $this->reply($message, $keyboard);

        $poll = new Poll();
        $poll->identifier = Customer::whereIdentifier($this->user->id)->first()->id;
        $poll->title = $title;
        $poll->questions = serialize($storage['questions']);
        $poll->save();
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function run()
    {
        $this->start();
    }
}

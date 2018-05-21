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
        $this->reply('Для створення нового опитування, спочатку надішли мені його назву.');
        $this->setContext($this, 'firstQuestion');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function firstQuestion()
    {
        $title = $this->update->getMessage()->text;
        $this->userStorage()->save(['last_poll' => ['title' => $title]]);
        $message = "Створено нове опитування: '{$title}'".PHP_EOL.PHP_EOL.
            "Будь ласка, надішли мені перше питання.";
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
        $message = "Добре. Тепер надішли мені інше питання.".PHP_EOL.PHP_EOL.
            "Коли ти додасиш достатньо питань, просто відправ мені /done, щоб опублікувати опитування.";
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
        $message = "👍 Опитування створено. Тепер ти можеш опублікувати його в групі або надіслати його своїм друзям у приватному повідомленні. Для цього натисни кнопку нижче або розпочни набирати повідомлення в будь-якому іншому чаті з @{$username} і вибери одне з твоїх опитувань для відправки.";
        $this->reply($message);

        $keyboard = Keyboard::make()
            ->inline()
            ->row(Keyboard::inlineButton([
                'text' => 'Опублікувати опитування',
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

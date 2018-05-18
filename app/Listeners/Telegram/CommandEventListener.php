<?php

namespace App\Listeners\Telegram;

use App\Entities\Customer;
use App\Entities\Poll;
use App\Events\Telegram\CommandEvent;
use App\Http\Conversations\Telegram\Flows\AnswerFlow;
use App\Http\Conversations\Telegram\Flows\NewPollFlow;
use App\Http\Conversations\Telegram\Flows\PollFlow;
use App\Http\Conversations\Telegram\Flows\StartFlow;
use Telegram;

class CommandEventListener
{
    /**
     * @param CommandEvent $event
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    public function handle(CommandEvent $event)
    {
        $command = $event->command;
        $update = $event->update;

        if ($command == '/start') {
            $flow = new StartFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->run();
        }

        if (preg_match('#^/count|cnt$#', trim($command))) {
            $quantity = Customer::where(['messenger' => Customer::TELEGRAM])->count();
            Telegram::bot()->sendMessage([
                'chat_id' => $update->getMessage()->from->id,
                'text' => 'All customers - ' . $quantity
            ]);
        }

        if (trim($command) == '/newpoll') {
            $flow = new NewPollFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->run();
        }

        if (trim($command) == '/done') {
            $flow = new NewPollFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->done();
        }

        if (preg_match('#^/start p[0-9]+$#', $command)) {

            $user = Customer::where([
                'messenger' => Customer::TELEGRAM,
                'identifier' => $update->getMessage()->from->id
            ])->first();

            if (is_null($user)) {
                $flow = new StartFlow();
                $flow->setUser($update->getMessage()->from);
                $flow->setUpdate($update);
                $flow->run();
            }

            $flow = new AnswerFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->run();
        }

        if (trim($command) == '/polls') {
            $flow = new PollFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->run();
        }

        if (trim($command) == '/help') {
            Telegram::bot()->sendMessage([
                'chat_id' => $update->getMessage()->from->id,
                'text' => view('telegram.help')->render()
            ]);
        }

        if (preg_match('#^/questions_p[0-9]+$#', $command)) {
            $flow = new PollFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->questions();
        }

        if (preg_match('#^/results_p[0-9]+$#', $command)) {
            $flow = new PollFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->results();
        }

        return;
    }
}

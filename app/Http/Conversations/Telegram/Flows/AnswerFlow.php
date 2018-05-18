<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Entities\Answer;
use App\Entities\Customer;
use App\Entities\Poll;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AnswerFlow extends Flow
{
    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function askQuestion()
    {
        $page = $this->getOption('page') ?? 1;
        $poll_id = $this->getOption('poll_id');

        /** @var Poll $poll */
        $poll = Poll::find($poll_id);
        $questions = unserialize($poll->questions);
        $question = $this->paginate($questions, 1, $page)->items();
        $this->reply(current($question));
        $this->setContext($this, 'getAnswer', ['poll_id' => $poll_id, 'page' => $page]);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function getAnswer()
    {
        $poll_id = $this->getOption('poll_id');
        $page = $this->getOption('page');

        $answer = $this->update->getMessage()->text;

        /** @var Answer $answers */
        $customer_id = Customer::whereIdentifier($this->user->id)->first()->id;
        $answers = Answer::where(['poll_id' => $poll_id, 'customer_id' => $customer_id])->first();
        is_null($answers) ? $answers = null : $answers = unserialize($answers->answers);
        $answers[] = $answer;
        Answer::updateOrCreate([
            'poll_id' => $poll_id,
            'customer_id' => $customer_id
        ], [
            'poll_id' => $poll_id,
            'customer_id' => $customer_id,
            'answers' => serialize($answers)
        ]);

        /** @var Poll $poll */
        $poll = Poll::find($poll_id);
        $questions = unserialize($poll->questions);
        $count = count($questions);

        if ($page == $count) {
            $this->reply('Thanks for the answers');
            $this->clearContext();
            return;
        }

        $this->setContext($this, 'askQuestion', ['poll_id' => $poll_id, 'page' => $page+1]);
        $this->askQuestion();
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function run()
    {
        $command = $this->update->getMessage()->text;
        $poll_id = preg_replace("/[^0-9]/", '', $command);
        $this->addOption('poll_id', $poll_id);

        $customer_id = Customer::whereIdentifier($this->user->id)->first()->id;
        Answer::updateOrCreate([
            'poll_id' => $poll_id,
            'customer_id' => $customer_id
        ], [
            'poll_id' => $poll_id,
            'customer_id' => $customer_id,
            'answers' => ''
        ]);

        $this->askQuestion();
    }

    private function paginate($items, $perPage = 1, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

<?php

namespace App\Http\Conversations\Telegram\Traits;

use App\Http\Conversations\Telegram\Flows\Flow;
use Telegram;
use Telegram\Bot\Objects\User;

trait SendMessages
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param string $message
     * @param array $keyboard
     * @return Telegram\Bot\Objects\Message
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function reply(string $message, $keyboard = [])
    {
        $params = [
            'chat_id' => $this->user->id,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if (count($keyboard) > 0) {
            $params['reply_markup'] = $keyboard;
        }

        return Telegram::bot()->sendMessage($params);
    }

    /**
     * @param string $message
     * @param int $message_id
     * @param array $keyboard
     * @return bool|Telegram\Bot\Objects\Message
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function editReply(string $message, int $message_id, $keyboard = [])
    {
        $params = [
            'chat_id' => $this->user->id,
            'message_id' => $message_id,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if (count($keyboard) > 0) {
            $params['reply_markup'] = $keyboard;
        }

        return Telegram::bot()->editMessageText($params);
    }

    /**
     * @param string $message
     * @param bool $show_alert
     * @return bool
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function answerCallbackQuery(string $message, bool $show_alert = false)
    {
        /**
         * @var Flow $this
         */
        $response = Telegram::bot()->answerCallbackQuery([
            'callback_query_id' => $this->update->callbackQuery->id,
            'text' => $message,
            'show_alert' => $show_alert
        ]);

        return $response ? response('ok', 200) : false;
    }

    protected function sendPhoto()
    {

    }
}

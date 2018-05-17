<?php
/**
 * @var \Telegram\Bot\Objects\User $user
 * @var int $quantity
 */
?>
〰〰〰〰〰〰〰〰〰〰
<b>🙍‍♂️New customer joined🙎‍♀️</b>
@if ($user->username)
Username: {{'@'.$user->username}}
@else
Username:
@endif
First name: {{$user->firstName}}
Last name: {{$user->lastName}}
Language: {{$user->languageCode}}
ID: {{$user->id}}
All customers: <b>{{$quantity}}</b>
〰〰〰〰〰〰〰〰〰〰

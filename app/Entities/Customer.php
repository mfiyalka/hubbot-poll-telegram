<?php

namespace App\Entities;

use App\Events\Telegram\NewTelegramUser;
use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Objects\User;

/**
 * App\Entities\Customer
 *
 * @property int $id
 * @property int $messenger
 * @property int $identifier
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereMessenger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereUsername($value)
 * @mixin \Eloquent
 *
 * @method static where($value)
 * @method static find($value)
 */
class Customer extends Model
{
    const TELEGRAM = 1;
    const FACEBOOK_MESSENGER = 2;
    const VIBER = 3;

    protected $fillable = [
        'messenger',
        'identifier',
        'first_name',
        'last_name',
        'username',
        'language',
    ];

    public static function addFromTelegram(User $user)
    {
        self::created(function () use ($user) {
            return event(new NewTelegramUser($user));
        });

        return self::updateOrCreate([
            'identifier' => $user->id,
            'messenger' => self::TELEGRAM
        ], [
            'is_bot' => $user->isBot,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'username' => $user->username,
            'language' => $user->languageCode ?? null
        ]);
    }
}

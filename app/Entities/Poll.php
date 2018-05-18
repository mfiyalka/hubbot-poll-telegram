<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Poll
 * @package App\Entities
 *
 * @property int $id
 * @property int $identifier
 * @property string $title
 * @property string $questions
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Poll whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Poll whereTitle($value)
 * @mixin \Eloquent
 *
 * @method static where($value)
 * @method static find($value)
 */
class Poll extends Model
{
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'identifier');
    }
}

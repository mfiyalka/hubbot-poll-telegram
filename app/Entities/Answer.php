<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Answer
 * @package App\Entities
 *
 * @property int $id
 * @property int $poll_id
 * @property int $customer_id
 * @property string $answers
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Poll wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Poll whereCustomerId($value)
 * @mixin \Eloquent
 *
 * @method static where($value)
 * @method static find($value)
 */
class Answer extends Model
{
    protected $fillable = [
        'poll_id',
        'customer_id',
        'answers'
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}

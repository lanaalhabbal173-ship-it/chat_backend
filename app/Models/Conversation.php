<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_one_id'
        );
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_two_id'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            Message::class
        );
    }

    public function latestMessage(): HasOne
    {
        return $this
            ->hasOne(Message::class)
            ->latestOfMany();
    }
}
<?php

namespace App\Models;

class Guest extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'guests';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

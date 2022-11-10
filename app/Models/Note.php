<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $table = 'notes';

    public $timestamps = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title', 'content'
    ];

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

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
        'title', 'content', 'creator_id'
    ];

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workspace(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function authorizedRoles()
    {
        return $this->belongsToMany(WorkspaceRole::class, 'notes_roles', 'note_id', 'role_id');
    }
}

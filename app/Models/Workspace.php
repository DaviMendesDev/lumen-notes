<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'creator_id'
    ];

    protected $table = 'workspaces';

    public function members(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspaces_users', 'workspace_id', 'user_id');
    }
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Note::class, 'workspace_id');
    }

    public function can(string $do)
    {

    }

    public function roles()
    {
        return $this->hasMany(WorkspaceRole::class);
    }

    public function setUpDefaultRoles()
    {
        /** @var WorkspaceRole $role */
        $role = $this->roles()->create([
            'name' => 'default',
            'characteristic_color' => '#FFFFFF',
        ]);

        $role->permissions()->attach(
            WorkspacePermissions::all()->map(fn ($el) => $el['id'])
        );
    }
}

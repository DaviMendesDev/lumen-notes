<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email',
    ];

    public $timestamps = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    private ?Workspace $workingWorkspace = null;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function notes(): \Illuminate\Database\Eloquent\Builder|HasMany
    {
        if ($this->workingWorkspace === null)
            return Note::query()->whereHas('workspace.members', function ($query) {
                $query->where('users.id', $this->id);
            });

        return $this->workingWorkspace->notes()->whereHas('workspace.members', function ($query) {
            $query->where('users.id', $this->id);
        });
    }

    public function workspaces(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspaces_users', 'user_id');
    }

//    All the methods below are methods meant to be auto-explanative
    public function prepareNewWorkspace(string $name)
    {
        /** @var Workspace $workspace */
        $workspace = $this->workspaces()->create([
            'name' => $name,
            'creator_id' => $this->id,
        ]);

        $workspace->setUpDefaultRoles();
        $this->addToMineRoles(
            $workspace->roles()->firstOrFail()
        );
    }

    public function addToMineRoles(int|WorkspaceRole $role)
    {
        if (is_int($role)) {
            $role = WorkspaceRole::query()->findOrFail($role);
        }

        $this->roles()->attach($role->id);
    }

    public function withWorkspace(string|int|Workspace $workspace): static
    {
        if ($workspace instanceof Workspace) {
            $this->workingWorkspace = $workspace;
        }

        if (is_string($workspace) || is_int($workspace)) {
            $this->workingWorkspace = $this->workspaces()->findOrFail($workspace);
        }

        return $this;
    }

    public function writeNewNote(string $title, string $content)
    {
        if ($this->workingWorkspace == null) {
            throw new \Exception('Working workspace not provided');
        }

        $this->workingWorkspace->notes()->create([
            'title' => $title,
            'content' => $content,
            'creator_id' => $this->id,
        ]);
    }

    public function writeInto(Note $note, string $content): bool
    {
        $note->content = $content;
        return $note->save();
    }

    public function changeTitle(Note $note, string $title): bool
    {
        $note->title = $title;
        return $note->save();
    }

    public function sendToTrash(Note $note): ?bool
    {
        return $note->delete();
    }

    public function isMine(Note $note): bool
    {
        return $this->id === $note->owner->id;
    }

    public function guest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guest::class, 'user_id');
    }

    public function getAllMineRoles()
    {
        return $this->roles()->where('workspace_id', $this->workingWorkspace->id);
    }

    public function canDo(string $action, Workspace $workspace)
    {
        return $this->withWorkspace($workspace)->getAllMineRoles()->get()
            ->contains(fn ($role) => $role->permissions->contains('slug', $action));
    }

    public function roles()
    {
        return $this->belongsToMany(WorkspaceRole::class, 'roles_users', 'user_id', 'workspace_role_id');
    }

    public function isGuest(): bool
    {
        return $this->guest()->exists();
    }
}

<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function writeNewNote(string $title, string $content)
    {
        $this->notes()->create([
            'title' => $title,
            'content' => $content
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
}

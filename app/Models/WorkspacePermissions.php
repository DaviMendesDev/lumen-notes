<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspacePermissions extends Model
{
    protected $fillable = [
        'label', 'slug', 'description', 'example_video',
    ];
}

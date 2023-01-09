<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceRole extends Model
{
    protected $fillable = [
        'name', 'can', 'characteristic_color'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function changeColorTo(string $color)
    {
        $this->characteristic_color = $color;
        $this->save();

        return $this;
    }

    public function permissions()
    {
        return $this->belongsToMany(WorkspacePermissions::class, 'roles_permissions', 'role_id', 'permission_id');
    }
}

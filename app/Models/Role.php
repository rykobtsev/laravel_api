<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_role', 'id_role', 'id_user');
    }

    public function delete()
    {
        $this->users()->detach();

        parent::delete();
    }
}

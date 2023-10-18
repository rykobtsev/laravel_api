<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'email',
        'email_verified_at',
        'password',
        'id_role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'active'            => 'boolean'
    ];

    public function userRole()
    {
        return $this->hasOne(UsersRole::class, 'id_user');
    }

    public function update(array $attributes = [], array $options = [])
    {
        if (isset($attributes['id_role'])) {
            $this->userRole->update(['id_role' => $attributes['id_role']]);
            unset($attributes['id_role']);
        }

        return parent::update($attributes, $options);
    }

    public function delete()
    {
        if ($this->userRole) {
            $this->userRole()->delete();
        }

        parent::delete();
    }

    public function isAdmin()
    {
        return $this->userRole->role->id === 1;
    }
}
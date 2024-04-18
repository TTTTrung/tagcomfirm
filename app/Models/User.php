<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password' => 'hashed',
    ];

    public function createParts(): HasMany
    {
        return $this->hasMany(Part::class, 'created_by');
    }

    public function updateParts(): HasMany
    {
        return $this->hasMany(Part::class, 'updated_by');
    }

    public function createPlandue(): HasMany
    {
        return $this->hasMany(Plandue::class, 'created_by');
    }

    public function createListiten():HasMany
    {
        return $this->hasMany(Listitem::class, 'created_by');
    }

    public function updateListitem():HasMany
    {
        return $this->hasmany(Listitem::class, 'updated_by');
    }
}

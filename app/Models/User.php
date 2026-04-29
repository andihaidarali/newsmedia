<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdministrator(): bool
    {
        return $this->role === Role::ADMINISTRATOR;
    }

    public function isEditor(): bool
    {
        return $this->role === Role::EDITOR || $this->role === Role::ADMINISTRATOR;
    }

    public function isReporter(): bool
    {
        return $this->role === Role::REPORTER;
    }

    public function canAccessAdminAdvertorials(): bool
    {
        return $this->isEditor();
    }

    public function isLoginAllowed(): bool
    {
        return $this->isAdministrator() || $this->is_active;
    }

    /**
     * Get the posts authored by this user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

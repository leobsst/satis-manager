<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication, MustVerifyEmail, OAuthenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * @var array<string>|null
     */
    protected $app_authentication_recovery_codes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

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
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
            'has_email_authentication' => 'boolean',
        ];
    }

    /**
     * Check if the user has a role to access the panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Determine if the user has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return filled($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    public function getAppAuthenticationSecret(): ?string
    {
        // This method should return the user's saved app authentication secret.

        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        // This method should save the user's app authentication secret.

        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        // In a user's authentication app, each account can be represented by a "holder name".
        // If the user has multiple accounts in your app, it might be a good idea to use
        // their email address as then they are still uniquely identifiable.

        return $this->email;
    }

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        // This method should return the user's saved app authentication recovery codes.

        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  array<string> | null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        // This method should save the user's app authentication recovery codes.

        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }

    public function hasEmailAuthentication(): bool
    {
        // This method should return true if the user has enabled email authentication.

        return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        // This method should save whether or not the user has enabled email authentication.

        $this->has_email_authentication = $condition;
        $this->save();
    }

    /**
     * Send the finalization email.
     */
    public function sendFinalizationEmail(): void
    {
        $token = app('auth.password.broker')->createToken($this);
        $subject = 'Bienvenue sur ' . config('app.name');
        self::notify(new ResetPasswordNotification(
            token: $token,
            subject: $subject,
            action: 'Définir mon mot de passe',
            l1: 'Votre compte vient d\'être créé avec succès.',
            l2: 'Pour vous connecter, veuillez cliquer sur le bouton ci-dessous afin de définir votre mot de passe.'
        ));
    }
}

<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'first_name', 'gender', 'birth_date', 'city', 'country', 'email', 'password', 'locale'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    public const ROLE_ADMIN = 'Admin';

    public const ROLE_MANAGER = 'Beheerder';

    public const ROLE_USER_PRO = 'user_pro';

    public const ROLE_USER = 'User';

    public const GENDER_MALE = 'man';

    public const GENDER_FEMALE = 'vrouw';

    public const GENDER_OTHER = 'anders';

    public const COUNTRY_NETHERLANDS = Organization::COUNTRY_NETHERLANDS;

    public const COUNTRY_BELGIUM = Organization::COUNTRY_BELGIUM;

    public const COUNTRY_GERMANY = Organization::COUNTRY_GERMANY;

    public const COUNTRY_FRANCE = Organization::COUNTRY_FRANCE;

    public const COUNTRY_UNITED_KINGDOM = Organization::COUNTRY_UNITED_KINGDOM;

    public const COUNTRY_UNITED_STATES = Organization::COUNTRY_UNITED_STATES;

    public const COUNTRY_OTHER = Organization::COUNTRY_OTHER;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'selected_strengths' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function genderOptions(): array
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE,
            self::GENDER_OTHER,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function countryOptions(): array
    {
        return Organization::countryOptions();
    }

    /**
     * @return array<int, string>
     */
    public static function localeOptions(): array
    {
        return array_keys(config('locales.supported', []));
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isProUser(): bool
    {
        return $this->role === self::ROLE_USER_PRO;
    }

    public function canAccessAdminPortal(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canManageOrganization(int $organizationId): bool
    {
        return $this->isAdmin() || $this->org_id === $organizationId;
    }

    /**
     * Anonymize the user's personal data for statistical purposes.
     *
     * Sets remember_token to null to invalidate all "remember me" cookies.
     * Deletes all active database sessions to immediately invalidate access
     * on all devices.
     */
    public function anonymizeForStatistics(): void
    {
        $anonymizedIdentifier = (string) $this->getKey();

        $this->forceFill([
            'name' => $anonymizedIdentifier,
            'first_name' => $anonymizedIdentifier,
            'email' => $this->anonymizedEmailAddress(),
            'email_verified_at' => null,
            'password' => Str::random(40),
            'remember_token' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        DB::table('sessions')->where('user_id', $this->getKey())->delete();
    }

    public function anonymizedEmailAddress(): string
    {
        return sprintf('deleted-user+%s@hermesresults.com', $this->getKey());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function questionnaireResponses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    public function isProfileComplete(): bool
    {
        return ! empty($this->first_name)
            && ! empty($this->gender)
            && ! empty($this->birth_date)
            && ! empty($this->city)
            && ! empty($this->country);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}

<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['naam', 'adres', 'postcode', 'plaats', 'land', 'telefoon', 'contact_id'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    public const COUNTRY_NETHERLANDS = 'Nederland';

    public const COUNTRY_BELGIUM = 'België';

    public const COUNTRY_GERMANY = 'Duitsland';

    public const COUNTRY_FRANCE = 'Frankrijk';

    public const COUNTRY_UNITED_KINGDOM = 'UK';

    public const COUNTRY_UNITED_STATES = 'VS';

    public const COUNTRY_OTHER = 'Anders';

    protected $primaryKey = 'org_id';

    /**
     * @return array<int, string>
     */
    public static function countryOptions(): array
    {
        return [
            self::COUNTRY_NETHERLANDS,
            self::COUNTRY_BELGIUM,
            self::COUNTRY_GERMANY,
            self::COUNTRY_FRANCE,
            self::COUNTRY_UNITED_KINGDOM,
            self::COUNTRY_UNITED_STATES,
            self::COUNTRY_OTHER,
        ];
    }

    public function scopeForActor(Builder $query, User $actor): void
    {
        if (! $actor->isAdmin()) {
            $query->where('org_id', $actor->org_id);
        }
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_id', 'org_id');
    }

    public function questionnaireAvailabilities(): HasMany
    {
        return $this->hasMany(OrganizationQuestionnaire::class, 'org_id', 'org_id');
    }
}

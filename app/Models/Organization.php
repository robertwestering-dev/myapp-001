<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['naam', 'adres', 'postcode', 'plaats', 'land', 'telefoon', 'contact_id'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    protected $primaryKey = 'org_id';

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

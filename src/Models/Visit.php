<?php

namespace Niladam\LaravelVisits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_id',
        'visitable_id',
        'visitable_type',
        'platform',
        'referrer',
        'url',
        'created_at',
        'updated_at',
    ];

    public function scopeDesktop(Builder $query): Builder
    {
        return $query->where('platform', 'desktop');
    }

    public function scopePhone(Builder $query): Builder
    {
        return $query->where('platform', 'phone');
    }

    public function scopeTablet(Builder $query): Builder
    {
        return $query->where('platform', 'tablet');
    }

    public function scopeMobile(Builder $query): Builder
    {
        return $query->whereIn('platform', ['phone', 'tablet']);
    }

    public function scopeRobot(Builder $query): Builder
    {
        return $query->where('platform', 'robot');
    }

    public function scopeOther(Builder $query): Builder
    {
        return $query->where('platform', 'other');
    }

    public function scopeEntities(Builder $query): Builder
    {
        return $query->where('visitable_type', '!=', null);
    }

    public function scopeUrls(Builder $query): Builder
    {
        return $query->where('visitable_type', null);
    }

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('laravel-visits.overwrites.user_model', '\App\Models\User'));
    }

    public function isMobile(): bool
    {
        return in_array($this->platform, ['phone', 'tablet']);
    }

    public function isDesktop(): bool
    {
        return $this->platform === 'desktop';
    }

    public function type(): string
    {
        return $this->platform;
    }

    public function isModel(): bool
    {
        return $this->visitable_type !== null;
    }

    protected function getIpAttribute()
    {
        return $this->ip_address;
    }

    protected function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => inet_ntop($value),
            set: fn ($value) => inet_pton($value),
        );
    }
}

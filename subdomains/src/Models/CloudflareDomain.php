<?php

namespace Boy132\Subdomains\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Boy132\Subdomains\Services\CloudflareService;
use Filament\Notifications\Notification;

/**
 * @property int $id
 * @property string $name
 * @property ?string $cloudflare_id
 * @property ?string $srv_target
 */
class CloudflareDomain extends Model
{
    protected $fillable = [
        'name',
        'cloudflare_id',
        'srv_target',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $model) {
            $service = new CloudflareService();

            $zoneId = $service->getZoneId($model->name);
            if (!$zoneId) {
                Notification::make()
                    ->title('Failed to fetch Cloudflare Zone ID for domain: ' . $model->name)
                    ->danger()
                    ->send();
            }

            Notification::make()
                ->title('Successfully saved domain: ' . $model->name)
                ->success()
                ->send();

            $model->update([
                'cloudflare_id' => $zoneId,
            ]);
        });
    }

    public function subdomains(): HasMany
    {
        return $this->hasMany(Subdomain::class, 'domain_id');
    }
}

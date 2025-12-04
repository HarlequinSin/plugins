<?php

namespace Boy132\Subdomains\Models;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

/**
 * @property int $id
 * @property string $name
 * @property string $record_type
 * @property int $domain_id
 * @property CloudflareDomain $domain
 * @property int $server_id
 * @property Server $server
 */
class Subdomain extends Model implements HasLabel
{
    protected $fillable = [
        'name',
        'record_type',
        'domain_id',
        'server_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $model) {
            $model->createOnCloudflare();
        });

        static::updated(function (self $model) {
            $model->updateOnCloudflare();
        });

        static::deleted(function (self $model) {
            $model->deleteOnCloudflare();
        });
    }

    public function domain(): BelongsTo
    {
        return $this->BelongsTo(CloudflareDomain::class, 'domain_id');
    }

    public function server(): BelongsTo
    {
        return $this->BelongsTo(Server::class);
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name . '.' . $this->domain->name;
    }

    protected function createOnCloudflare(): void
    {
        /*$key = new \Cloudflare\API\Auth\APIKey(config('subdomains.email'), config('subdomains.key'));
        $adapter = new \Cloudflare\API\Adapter\Guzzle($key);
        $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
        $dns = new \Cloudflare\API\Endpoints\DNS($adapter);

        $zoneID = $zones->getZoneID($this->name);

        $dns->addRecord($zoneID, $this->record_type, $this->name, $this->server->allocation->ip, 120, false);*/

        $zones = Http::cloudflare()->get('zones', [
            'page' => 1,
            'per_page' => 20,
            'match' => 'all',
            'type' => $this->record_type,
            'name' => $this->domain->name,
        ])->json('result');

        Http::cloudflare()->post("zones/{$zones[0]->id}/dns_records", [
            'type' => $this->record_type,
            'name' => $this->name,
            'content' => $this->server->allocation->ip,
            'proxied' => false,
            'ttl' => 120,
        ]);
    }

    protected function updateOnCloudflare(): void
    {
        /*$key = new \Cloudflare\API\Auth\APIKey(config('subdomains.email'), config('subdomains.key'));
        $adapter = new \Cloudflare\API\Adapter\Guzzle($key);
        $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
        $dns = new \Cloudflare\API\Endpoints\DNS($adapter);

        $zoneID = $zones->getZoneID($this->name);

        $result = $dns->listRecords($zoneID, $this->record_type, $this->getLabel())->result;

        $dns->updateRecordDetails($zoneID, $result[0]->id, [
            'type' => $this->record_type,
            'name' => $this->name,
            'content' => $this->server->allocation->ip,
            'ttl' => 120,
            'proxied' => false,
        ]);*/

        $zones = Http::cloudflare()->get('zones', [
            'page' => 1,
            'per_page' => 20,
            'match' => 'all',
            'type' => $this->record_type,
            'name' => $this->domain->name,
        ])->json('result');

        $records = Http::cloudflare()->get("zones/{$zones[0]->id}/dns_records", [
            'page' => 1,
            'per_page' => 20,
            'match' => 'all',
            'type' => $this->record_type,
            'name' => $this->name,
        ])->json('result');

        Http::cloudflare()->post("zones/{$zones[0]->id}/dns_records/{$records[0]->id}", [
            'type' => $this->record_type,
            'name' => $this->name,
            'content' => $this->server->allocation->ip,
            'proxied' => false,
            'ttl' => 120,
        ]);
    }

    protected function deleteOnCloudflare(): void
    {
        /*$key = new \Cloudflare\API\Auth\APIKey(config('subdomains.email'), config('subdomains.key'));
        $adapter = new \Cloudflare\API\Adapter\Guzzle($key);
        $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
        $dns = new \Cloudflare\API\Endpoints\DNS($adapter);

        $zoneID = $zones->getZoneID($this->name);

        $result = $dns->listRecords($zoneID, $this->record_type, $this->getLabel())->result;

        $dns->deleteRecord($zoneID, $result[0]->id);*/

        $zones = Http::cloudflare()->get('zones', [
            'page' => 1,
            'per_page' => 20,
            'match' => 'all',
            'type' => $this->record_type,
            'name' => $this->domain->name,
        ])->json('result');

        $records = Http::cloudflare()->get("zones/{$zones[0]->id}/dns_records", [
            'page' => 1,
            'per_page' => 20,
            'match' => 'all',
            'type' => $this->record_type,
            'name' => $this->name,
        ])->json('result');

        Http::cloudflare()->delete("zones/{$zones[0]->id}/dns_records/{$records[0]->id}");
    }
}

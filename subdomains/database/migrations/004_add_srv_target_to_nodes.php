<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cloudflare_domains')->whereNull('cloudflare_id')->delete();
        DB::table('subdomains')->whereNull('cloudflare_id')->delete();

        Schema::table('cloudflare_domains', function (Blueprint $table) {
            if (Schema::hasColumn('cloudflare_domains', 'srv_target')) {
                $table->dropColumn('srv_target');
            }

            if (config('database.default') !== 'sqlite') {
                $table->string('cloudflare_id')->nullable(false)->change();
            }
        });

        Schema::table('subdomains', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->string('cloudflare_id')->nullable(false)->change();
            }
        });

        Schema::table('nodes', function (Blueprint $table) {
            if (!Schema::hasColumn('nodes', 'srv_target')) {
                $table->string('srv_target')->nullable()->after('fqdn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            if (!Schema::hasColumn('cloudflare_domains', 'srv_target')) {
                $table->string('srv_target')->nullable();
            }

            if (config('database.default') !== 'sqlite') {
                $table->string('cloudflare_id')->nullable()->change();
            }
        });

        Schema::table('subdomains', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->string('cloudflare_id')->nullable()->change();
            }
        });

        Schema::table('nodes', function (Blueprint $table) {
            if (Schema::hasColumn('nodes', 'srv_target')) {
                $table->dropColumn('srv_target');
            }
        });
    }
}

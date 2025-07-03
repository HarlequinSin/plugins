<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('priority');
            $table->text('description')->nullable();
            $table->boolean('is_answered')->default(false);
            $table->text('answer')->nullable();
            $table->foreignIdFor(Server::class, 'server_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignIdFor(User::class, 'assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

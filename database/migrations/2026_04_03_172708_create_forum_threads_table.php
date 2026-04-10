<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forum_threads')) {
            return;
        }

        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('discussion_type', 32)->index();
            $table->string('title', 160);
            $table->longText('body');
            $table->json('tags')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();

            $table->index(['discussion_type', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};

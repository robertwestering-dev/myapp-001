<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('three_good_things_entries')) {
            return;
        }

        Schema::create('three_good_things_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('what_went_well', 255);
            $table->string('my_contribution', 255);
            $table->timestamps();

            $table->unique(['user_id', 'entry_date']);
            $table->index(['user_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('three_good_things_entries');
    }
};

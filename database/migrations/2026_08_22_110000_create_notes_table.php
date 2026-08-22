<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecon_id')->constrained('lecons')->onDelete('cascade');
            $table->text('content');
            $table->integer('timestamp_seconds')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'lecon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};

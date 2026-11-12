<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('chat_entries')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['parent_id', 'position']);
        });

        Schema::create('chat_entry_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_entry_id')->constrained('chat_entries')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('question');
            $table->text('answer');
            $table->string('link_label')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->timestamps();

            $table->unique(['chat_entry_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_entry_translations');
        Schema::dropIfExists('chat_entries');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('brochure_url', 2048)->nullable();
            $table->string('video_url', 2048)->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('tds_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('material_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['material_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('material_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->string('label', 100);
            $table->string('url', 2048);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['material_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_links');
        Schema::dropIfExists('material_translations');
        Schema::dropIfExists('materials');
    }
};

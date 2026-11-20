<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
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

        Schema::create('product_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('product_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('label', 100);
            $table->string('url', 2048);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_links');
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('products');
    }
};

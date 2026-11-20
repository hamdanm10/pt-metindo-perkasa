<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_category_id')->constrained('process_categories')->restrictOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['process_category_id', 'position']);
        });

        Schema::create('process_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_id')->constrained('processes')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['process_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('process_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_id')->constrained('processes')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['process_id', 'position']);
        });

        Schema::create('process_step_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_step_id')->constrained('process_steps')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['process_step_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('process_step_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_step_id')->constrained('process_steps')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);

            $table->unique(['process_step_id', 'product_id']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_step_product');
        Schema::dropIfExists('process_step_translations');
        Schema::dropIfExists('process_steps');
        Schema::dropIfExists('process_translations');
        Schema::dropIfExists('processes');
    }
};

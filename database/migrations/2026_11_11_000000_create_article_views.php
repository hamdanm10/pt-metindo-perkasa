<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_translations', function (Blueprint $table): void {
            $table->unsignedBigInteger('views_count')->default(0)->index();
        });

        Schema::create('article_view_days', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_translation_id')->constrained()->cascadeOnDelete();
            $table->date('day');
            $table->unsignedInteger('views')->default(0);

            $table->unique(['article_translation_id', 'day']);
            $table->index('day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_view_days');

        Schema::table('article_translations', function (Blueprint $table): void {
            $table->dropColumn('views_count');
        });
    }
};

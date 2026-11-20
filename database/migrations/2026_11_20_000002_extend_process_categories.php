<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_categories', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);

            $table->index('position');
        });

        Schema::create('process_category_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_category_id')->constrained('process_categories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['process_category_id', 'locale']);
            $table->index('locale');
        });

        $now = now();
        $taken = [];

        DB::table('process_categories')->orderBy('name')->orderBy('id')->get()->each(function (object $category, int $position) use ($now, &$taken): void {
            $base = Str::slug($category->name) ?: 'category-'.$category->id;
            $slug = $base;

            for ($suffix = 2; in_array($slug, $taken, true); $suffix++) {
                $slug = $base.'-'.$suffix;
            }

            $taken[] = $slug;

            DB::table('process_categories')->where('id', $category->id)->update(['slug' => $slug, 'position' => $position]);

            if ($category->description !== null) {
                DB::table('process_category_translations')->insert([
                    'process_category_id' => $category->id,
                    'locale' => 'en',
                    'description' => $category->description,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        Schema::table('process_categories', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('process_categories', function (Blueprint $table): void {
            $table->text('description')->nullable();
        });

        DB::table('process_category_translations')->where('locale', 'en')->orderBy('id')->each(function (object $translation): void {
            DB::table('process_categories')->where('id', $translation->process_category_id)->update(['description' => $translation->description]);
        });

        Schema::dropIfExists('process_category_translations');

        Schema::table('process_categories', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropIndex(['position']);
            $table->dropColumn(['slug', 'position', 'is_active']);
        });
    }
};

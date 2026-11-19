<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);

            $table->index('position');
        });

        Schema::create('service_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            $table->json('included')->nullable();
            $table->text('fit')->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'locale']);
            $table->index('locale');
        });

        $now = now();

        DB::table('services')->orderBy('id')->each(function (object $service) use ($now): void {
            DB::table('service_translations')->insert([
                'service_id' => $service->id,
                'locale' => 'en',
                'title' => $service->name,
                'description' => $service->description,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->text('description')->nullable();
        });

        DB::table('service_translations')->where('locale', 'en')->orderBy('id')->each(function (object $translation): void {
            DB::table('services')->where('id', $translation->service_id)->update(['description' => $translation->description]);
        });

        Schema::dropIfExists('service_translations');

        Schema::table('services', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropIndex(['position']);
            $table->dropColumn(['slug', 'image_path', 'position', 'is_active']);
        });
    }
};

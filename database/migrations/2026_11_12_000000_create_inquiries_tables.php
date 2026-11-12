<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table): void {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->date('asked_at')->index();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('service_id');
            $table->index('product_id');
            $table->index('created_by');
        });

        Schema::create('inquiry_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();

            $table->index('inquiry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_images');
        Schema::dropIfExists('inquiries');
    }
};

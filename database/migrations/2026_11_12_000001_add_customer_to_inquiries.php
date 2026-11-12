<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table): void {
            $table->foreignId('customer_id')->after('id')->constrained('users');
            $table->index(['customer_id', 'asked_at']);
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table): void {
            $table->dropIndex(['customer_id', 'asked_at']);
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};

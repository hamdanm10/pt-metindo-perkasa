<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('products', 'process_categories');
        Schema::table('inquiries', function ($table): void {
            $table->renameColumn('product_id', 'process_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function ($table): void {
            $table->renameColumn('process_category_id', 'product_id');
        });
        Schema::rename('process_categories', 'products');
    }
};

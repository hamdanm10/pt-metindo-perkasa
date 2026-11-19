<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Answers are now rich text kept as HTML. Plain-text answers become escaped paragraphs.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('inquiries')->orderBy('id')->each(function (object $row): void {
            $paragraphs = preg_split('/\R{2,}/', trim((string) $row->answer)) ?: [];
            $html = implode('', array_map(
                fn (string $text): string => '<p>'.nl2br(e(trim($text)), false).'</p>',
                array_filter($paragraphs, fn (string $text): bool => trim($text) !== ''),
            ));

            DB::table('inquiries')->where('id', $row->id)->update(['answer' => $html]);
        });
    }

    public function down(): void
    {
        DB::table('inquiries')->orderBy('id')->each(function (object $row): void {
            $text = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ["\n", "\n", "\n", "\n\n"], (string) $row->answer)), ENT_QUOTES | ENT_HTML5);

            DB::table('inquiries')->where('id', $row->id)->update(['answer' => trim($text)]);
        });
    }
};

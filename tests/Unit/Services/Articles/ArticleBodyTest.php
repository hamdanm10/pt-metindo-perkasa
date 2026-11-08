<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Articles;

use App\Services\Articles\ArticleBody;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArticleBodyTest extends TestCase
{
    /**
     * @param  list<array<string, mixed>>  $content
     */
    private function html(array $content): string
    {
        $body = new ArticleBody;

        return $body->render(['type' => 'doc', 'content' => $content]);
    }

    public function test_it_renders_allowed_nodes_and_marks(): void
    {
        $html = $this->html([
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Title']]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Bold', 'marks' => [['type' => 'bold']]]]],
        ]);

        $this->assertSame('<h2>Title</h2><p><strong>Bold</strong></p>', $html);
    }

    public function test_text_is_escaped_and_raw_html_never_renders(): void
    {
        $html = $this->html([['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '<script>alert(1)</script>']]]]);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_unsafe_link_addresses_are_dropped_and_safe_links_get_rel(): void
    {
        $unsafe = $this->html([['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'x', 'marks' => [['type' => 'link', 'attrs' => ['href' => 'javascript:alert(1)']]]]]]]);
        $safe = $this->html([['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'x', 'marks' => [['type' => 'link', 'attrs' => ['href' => 'https://example.com']]]]]]]);

        $this->assertStringNotContainsString('javascript:', $unsafe);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $safe);
    }

    public function test_images_code_and_rules_are_not_rendered(): void
    {
        $html = $this->html([
            ['type' => 'image', 'attrs' => ['src' => 'https://example.com/a.png']],
            ['type' => 'codeBlock', 'content' => [['type' => 'text', 'text' => 'code']]],
            ['type' => 'horizontalRule'],
        ]);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<pre', $html);
        $this->assertStringNotContainsString('<hr', $html);
    }

    public function test_it_rejects_a_body_that_is_not_a_document(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ArticleBody)->parse('{"type":"paragraph"}');
    }

    public function test_it_knows_when_a_document_is_empty(): void
    {
        $body = new ArticleBody;

        $this->assertTrue($body->isEmpty(['type' => 'doc', 'content' => [['type' => 'paragraph']]]));
        $this->assertFalse($body->isEmpty(['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'a']]]]]));
    }
}

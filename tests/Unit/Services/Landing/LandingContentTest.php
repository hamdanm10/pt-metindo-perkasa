<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Landing;

use App\Services\Landing\LandingContent;
use Tests\TestCase;

class LandingContentTest extends TestCase
{
    public function test_it_finds_a_category_by_slug(): void
    {
        $content = new LandingContent;

        $this->assertSame('Zinc Plating Chemicals', $content->category('zinc-plating')['name'] ?? null);
        $this->assertNull($content->category('unknown'));
    }

    public function test_sample_content_is_empty_when_the_flag_is_off(): void
    {
        config(['landing.sample_content' => false]);

        $content = new LandingContent;

        $this->assertSame([], $content->articles());
        $this->assertSame([], $content->subsidiaries());
        $this->assertNull($content->article('quality-control-in-plating'));
    }

    public function test_articles_can_be_filtered_by_type(): void
    {
        config(['landing.sample_content' => true]);

        $content = new LandingContent;

        $types = array_unique(array_column($content->articles('news'), 'type'));

        $this->assertSame(['news'], $types);
        $this->assertCount(4, $content->articles());
    }

    public function test_menu_lists_articles_only_with_sample_content(): void
    {
        config(['landing.sample_content' => true]);
        $this->assertContains('landing.articles.index', array_column((new LandingContent)->menu(), 'route'));

        config(['landing.sample_content' => false]);
        $this->assertNotContains('landing.articles.index', array_column((new LandingContent)->menu(), 'route'));
    }
}

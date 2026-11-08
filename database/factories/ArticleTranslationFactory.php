<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PublicationStatus;
use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ArticleTranslation>
 */
class ArticleTranslationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'article_id' => Article::factory(),
            'locale' => 'en',
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => $title,
            'excerpt' => fake()->sentence(),
            'body' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraph()]]]]],
            'body_html' => '<p>'.fake()->paragraph().'</p>',
            'meta_title' => null,
            'meta_description' => fake()->sentence(),
            'image_alt' => fake()->words(3, true),
            'status' => PublicationStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(?string $locale = null): static
    {
        return $this->state(fn (): array => array_filter([
            'locale' => $locale,
            'status' => PublicationStatus::Published,
            'published_at' => now()->subDay(),
        ]));
    }
}

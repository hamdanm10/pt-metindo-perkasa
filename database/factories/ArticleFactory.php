<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ArticleType;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => ArticleType::Article,
            'thumbnail_path' => 'articles/'.fake()->uuid().'.webp',
        ];
    }

    public function news(): static
    {
        return $this->state(['type' => ArticleType::News]);
    }
}

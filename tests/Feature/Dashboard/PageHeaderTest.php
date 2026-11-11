<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageHeaderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['/app/dashboard', 'dashboard.home.description'],
            'staff list' => ['/app/staff', 'users.kinds.staff.description'],
            'staff create' => ['/app/staff/create', 'users.kinds.staff.create_description'],
            'customers list' => ['/app/customers', 'users.kinds.customer.description'],
            'customers create' => ['/app/customers/create', 'users.kinds.customer.create_description'],
            'articles list' => ['/app/articles', 'article_manager.description'],
            'articles create' => ['/app/articles/create', 'article_manager.create_description'],
            'products list' => ['/app/products', 'products.description'],
            'products create' => ['/app/products/create', 'products.create_description'],
            'services list' => ['/app/services', 'services.description'],
            'services create' => ['/app/services/create', 'services.create_description'],
        ];
    }

    #[DataProvider('pages')]
    public function test_every_admin_page_has_a_short_description_under_a_smaller_title(string $url, string $key): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['en', 'id'] as $locale) {
            $admin->forceFill(['locale' => $locale])->save();

            $html = (string) $this->actingAs($admin)->get($url)->assertOk()->getContent();

            $heading = (int) strpos($html, '<h1');
            $this->assertGreaterThan($heading, (int) strpos($html, e(__($key, [], $locale))), "{$url} ({$locale}) has no description after its title");
        }

        $this->assertStringContainsString('text-[clamp(1.5rem,2.2vw,2rem)]', (string) $this->get($url)->getContent());
    }

    public function test_the_edit_pages_have_a_description_and_the_users_email(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create(['email' => 'rina@example.test']);

        $this->actingAs($admin)->get("/app/staff/{$staff->id}/edit")
            ->assertSee(__('users.kinds.staff.edit_description'))
            ->assertSee('rina@example.test');

        $product = Product::query()->create(['name' => 'Zinc']);
        $service = Service::query()->create(['name' => 'Plating']);

        $this->get("/app/products/{$product->id}/edit")->assertSee(__('products.edit_description'));
        $this->get("/app/services/{$service->id}/edit")->assertSee(__('services.edit_description'));
    }
}

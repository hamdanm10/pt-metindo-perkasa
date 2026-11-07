<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class DashboardLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_default_locale_is_english(): void
    {
        $this->get('/app/login')->assertSee(__('auth.staff_title', [], 'en'));
    }

    public function test_the_session_locale_is_used_before_sign_in(): void
    {
        $this->withSession(['locale' => 'id'])->get('/app/login')->assertSee(__('auth.staff_title', [], 'id'));
    }

    public function test_the_user_locale_wins_over_the_session(): void
    {
        $user = User::factory()->staff()->create(['locale' => 'id']);

        $this->actingAs($user)->withSession(['locale' => 'en'])->get('/app/dashboard')
            ->assertSee(__('auth.signed_in_as', ['name' => $user->name], 'id'));
    }

    public function test_a_locale_outside_the_dashboard_list_falls_back_to_english(): void
    {
        $this->withSession(['locale' => 'zh'])->get('/app/login')->assertSee(__('auth.staff_title', [], 'en'));

        $user = User::factory()->staff()->create(['locale' => 'ar']);

        $this->actingAs($user)->get('/app/dashboard')->assertSee(__('auth.signed_in_as', ['name' => $user->name], 'en'));
    }

    public function test_dashboard_translation_keys_match_in_both_locales(): void
    {
        $keys = fn (string $code): array => array_keys(Arr::dot(require lang_path("{$code}/auth.php")));

        $this->assertEqualsCanonicalizing($keys('en'), $keys('id'));
    }
}

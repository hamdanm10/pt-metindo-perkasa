<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return TestResponse<Response>
     */
    private function attempt(string $area, string $email, string $password = 'password'): TestResponse
    {
        return $this->from("/{$area}/login")->post("/{$area}/login", ['email' => $email, 'password' => $password]);
    }

    public function test_admin_and_staff_sign_in_at_the_staff_login(): void
    {
        foreach (['admin', 'staff'] as $role) {
            $user = User::factory()->{$role}()->create();

            $this->attempt('app', $user->email)->assertRedirect('/app/dashboard');
            $this->assertAuthenticatedAs($user);

            $this->post('/app/logout');
        }
    }

    public function test_customer_signs_in_at_the_customer_login(): void
    {
        $user = User::factory()->customer()->create();

        $this->attempt('customer', $user->email)->assertRedirect('/customer/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_role_from_another_area_fails_like_a_wrong_password(): void
    {
        $customer = User::factory()->customer()->create();
        $staff = User::factory()->staff()->create();

        $this->attempt('app', $customer->email)->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->assertGuest();

        $this->attempt('customer', $staff->email)->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->assertGuest();
    }

    public function test_wrong_password_unknown_email_and_inactive_accounts_share_one_message(): void
    {
        $active = User::factory()->staff()->create();
        $inactive = User::factory()->staff()->inactive()->create();

        $this->attempt('app', $active->email, 'wrong')->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->attempt('app', 'nobody@example.test')->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->attempt('app', $inactive->email)->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->assertGuest();
    }

    public function test_email_is_matched_ignoring_case(): void
    {
        $user = User::factory()->admin()->create(['email' => 'boss@example.test']);

        $this->attempt('app', 'BOSS@Example.Test')->assertRedirect('/app/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_empty_and_malformed_input_is_rejected_by_validation(): void
    {
        $this->post('/app/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->post('/app/login', ['email' => 'not-an-email', 'password' => 'x'])->assertSessionHasErrors(['email']);
    }

    public function test_the_sixth_failed_attempt_is_throttled(): void
    {
        $user = User::factory()->staff()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->attempt('app', $user->email, 'wrong')->assertSessionHasErrors(['email' => __('auth.failed')]);
        }

        $response = $this->attempt('app', $user->email);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('seconds', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_a_successful_sign_in_clears_the_failed_attempts(): void
    {
        $user = User::factory()->staff()->create();

        for ($i = 0; $i < 4; $i++) {
            $this->attempt('app', $user->email, 'wrong');
        }

        $this->attempt('app', $user->email)->assertRedirect('/app/dashboard');
        $this->post('/app/logout');

        for ($i = 0; $i < 4; $i++) {
            $this->attempt('app', $user->email, 'wrong')->assertSessionHasErrors(['email' => __('auth.failed')]);
        }
    }

    public function test_the_session_id_changes_on_sign_in(): void
    {
        $user = User::factory()->staff()->create();

        $this->startSession();
        $before = session()->getId();

        $this->attempt('app', $user->email);

        $this->assertNotSame($before, session()->getId());
    }

    public function test_sign_out_ends_the_session_and_returns_to_the_right_login(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->post('/app/logout')
            ->assertRedirect('/app/login');
        $this->assertGuest();

        $this->actingAs(User::factory()->customer()->create())
            ->post('/customer/logout')
            ->assertRedirect('/customer/login');
        $this->assertGuest();
    }

    public function test_login_page_has_the_back_link_the_password_toggle_and_translated_texts(): void
    {
        foreach (['/app/login', '/customer/login'] as $path) {
            $this->get($path)->assertOk()
                ->assertSee('href="'.route('landing.home', ['locale' => 'en']).'"', false)
                ->assertSee('data-password-toggle="password"', false)
                ->assertSee('data-login-form', false)
                ->assertSee(__('auth.caps_lock'))
                ->assertSee(__('auth.intro'))
                ->assertSee(__('auth.panel_title'))
                ->assertSee('aria-label="'.__('auth.back_to_site').'"', false);
        }

        $this->withSession(['locale' => 'id'])->get('/app/login')
            ->assertSee(__('auth.show_password', [], 'id'))
            ->assertSee(__('auth.back_to_site', [], 'id'));
    }

    public function test_login_pages_render_with_a_title_and_a_csrf_token(): void
    {
        $this->get('/app/login')->assertOk()->assertSee(__('auth.staff_title'))->assertSee('name="_token"', false);
        $this->get('/customer/login')->assertOk()->assertSee(__('auth.customer_title'));
    }
}

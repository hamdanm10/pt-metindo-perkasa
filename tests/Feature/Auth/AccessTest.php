<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_of_the_area_they_asked_for(): void
    {
        $this->get('/app/dashboard')->assertRedirect('/app/login');
        $this->get('/customer/dashboard')->assertRedirect('/customer/login');
        $this->get('/app')->assertRedirect('/app/login');
    }

    public function test_a_page_remembered_from_another_area_is_ignored_after_sign_in(): void
    {
        $customer = User::factory()->customer()->create();

        $this->get('/app/dashboard')->assertRedirect('/app/login');

        $this->post('/customer/login', ['email' => $customer->email, 'password' => 'password'])
            ->assertRedirect('/customer/dashboard');
    }

    public function test_the_remembered_page_is_used_when_it_belongs_to_the_area(): void
    {
        $staff = User::factory()->staff()->create();

        $this->get('/app/dashboard')->assertRedirect('/app/login');

        $this->post('/app/login', ['email' => $staff->email, 'password' => 'password'])
            ->assertRedirect('/app/dashboard');
    }

    public function test_signed_in_users_visiting_a_login_page_are_sent_to_their_area(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/login')->assertRedirect('/app/dashboard');
        $this->actingAs(User::factory()->customer()->create())->get('/customer/login')->assertRedirect('/customer/dashboard');
        $this->actingAs(User::factory()->customer()->create())->get('/app/login')->assertRedirect('/customer/dashboard');
    }

    public function test_each_role_reaches_its_own_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertOk();
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertOk();
        $this->actingAs(User::factory()->customer()->create())->get('/customer/dashboard')->assertOk();
    }

    public function test_a_role_from_another_area_gets_forbidden(): void
    {
        $this->actingAs(User::factory()->customer()->create())->get('/app/dashboard')->assertForbidden();
        $this->actingAs(User::factory()->staff()->create())->get('/customer/dashboard')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/customer/dashboard')->assertForbidden();
    }

    public function test_a_deactivated_account_is_signed_out_on_its_next_request(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user)->get('/app/dashboard')->assertOk();

        $user->forceFill(['is_active' => false])->save();

        $this->get('/app/dashboard')->assertRedirect('/app/login');
        $this->assertGuest();
    }
}

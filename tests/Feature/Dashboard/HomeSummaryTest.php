<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Inquiry;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_account_counts_and_the_latest_records(): void
    {
        $customer = User::factory()->customer()->create(['name' => 'Budi Customer']);
        User::factory()->distributor()->count(2)->create();
        Inquiry::factory()->create(['customer_id' => $customer->id, 'question' => 'How long is the warranty?']);
        Purchase::factory()->create(['buyer_id' => $customer->id, 'invoice_number' => 'INV-777']);

        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')
            ->assertOk()
            ->assertSee(__('dashboard.nav.customers'))
            ->assertSee(__('dashboard.nav.distributors'))
            ->assertSee('How long is the warranty?')
            ->assertSee('INV-777')
            ->assertSee('Budi Customer')
            ->assertSee(route('app.customer-purchases.index'), false);
    }

    public function test_staff_does_not_see_account_counts(): void
    {
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')
            ->assertOk()
            ->assertDontSee(route('app.customers.index'), false)
            ->assertSee(route('app.customer-inquiries.index'), false)
            ->assertSee(__('dashboard.home.empty_text'));
    }

    public function test_a_customer_only_sees_their_own_records(): void
    {
        $customer = User::factory()->customer()->create();
        $other = User::factory()->customer()->create();
        Inquiry::factory()->create(['customer_id' => $customer->id, 'question' => 'Mine?']);
        Inquiry::factory()->create(['customer_id' => $other->id, 'question' => 'Theirs?']);
        Purchase::factory()->create(['buyer_id' => $customer->id, 'invoice_number' => 'INV-MINE']);
        Purchase::factory()->create(['buyer_id' => $other->id, 'invoice_number' => 'INV-THEIRS']);

        $this->actingAs($customer)->get('/customer/dashboard')
            ->assertOk()
            ->assertSee('Mine?')
            ->assertSee('INV-MINE')
            ->assertDontSee('Theirs?')
            ->assertDontSee('INV-THEIRS');
    }

    public function test_a_distributor_links_to_their_own_area(): void
    {
        $distributor = User::factory()->distributor()->create();
        $purchase = Purchase::factory()->create(['buyer_id' => $distributor->id]);

        $this->actingAs($distributor)->get('/distributor/dashboard')
            ->assertOk()
            ->assertSee(route('distributor.purchases.show', $purchase), false)
            ->assertDontSee('/customer/', false);
    }

    public function test_the_home_texts_follow_the_dashboard_locale(): void
    {
        $user = User::factory()->staff()->create(['locale' => 'id']);

        $this->actingAs($user)->get('/app/dashboard')
            ->assertSee(__('dashboard.home.recent_purchases', [], 'id'));
    }

    public function test_the_query_count_does_not_grow_with_the_rows(): void
    {
        $count = function (): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->get('/app/dashboard')->assertOk();

            return count(DB::getQueryLog());
        };

        $this->actingAs(User::factory()->admin()->create());
        Inquiry::factory()->create();
        Purchase::factory()->create();
        $before = $count();

        Inquiry::factory()->count(4)->create();
        Purchase::factory()->count(4)->create();

        $this->assertSame($before, $count());
    }
}

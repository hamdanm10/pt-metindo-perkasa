<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Livewire\Inquiries\Editor;
use App\Livewire\Inquiries\Index;
use App\Livewire\Lookup;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\Lookup\LookupSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class LookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_nothing_is_loaded_until_the_dropdown_opens(): void
    {
        User::factory()->customer()->create(['name' => 'Alice Customer']);
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Lookup::class, ['source' => 'customer'])
            ->assertDontSee('Alice Customer')
            ->call('openPanel')
            ->assertSee('Alice Customer');
    }

    public function test_the_search_matches_names_and_emails_without_regard_to_case_and_treats_wildcards_literally(): void
    {
        User::factory()->customer()->create(['name' => 'Alice Customer', 'email' => 'alice@example.test']);
        User::factory()->customer()->create(['name' => 'Bob Buyer', 'email' => 'bob@shop.test']);
        User::factory()->staff()->create(['name' => 'Alice Staff']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Lookup::class, ['source' => 'customer'])
            ->call('openPanel')
            ->set('search', 'ALICE')->assertSee('Alice Customer')->assertDontSee('Bob Buyer')->assertDontSee('Alice Staff')
            ->set('search', 'shop.test')->assertSee('Bob Buyer')->assertDontSee('Alice Customer')
            ->set('search', '%')->assertDontSee('Bob Buyer')->assertSee(__('dashboard.lookup.no_results'));
    }

    public function test_only_the_first_twenty_matches_load_and_the_user_is_told_there_are_more(): void
    {
        foreach (range(1, 25) as $number) {
            Service::factory()->create(['name' => sprintf('Service %02d', $number)]);
        }

        $search = new LookupSearch;
        $found = $search->search('service', '');

        $this->assertCount(20, $found['items']);
        $this->assertTrue($found['more']);
        $this->assertSame('Service 01', $found['items'][0]['label']);

        $narrow = $search->search('service', 'Service 2');
        $this->assertCount(6, $narrow['items']);
        $this->assertFalse($narrow['more']);

        Livewire::actingAs(User::factory()->staff()->create())->test(Lookup::class, ['source' => 'service'])
            ->call('openPanel')
            ->assertSee('Service 20')->assertDontSee('Service 21')
            ->assertSee(__('dashboard.lookup.more', ['count' => 20]));
    }

    public function test_choosing_an_option_sets_the_value_and_the_label_and_clearing_empties_it(): void
    {
        $product = Product::factory()->create(['name' => 'Zinc Plate']);

        Livewire::actingAs(User::factory()->staff()->create())->test(Lookup::class, ['source' => 'product', 'emptyLabel' => 'None', 'placeholder' => 'None'])
            ->call('openPanel')
            ->call('select', (string) $product->id)
            ->assertSet('value', (string) $product->id)
            ->assertSet('opened', false)
            ->assertSee('Zinc Plate')
            ->call('openPanel')
            ->assertSee('None')
            ->call('clear')
            ->assertSet('value', '');
    }

    public function test_an_id_that_does_not_exist_or_is_not_a_customer_cannot_be_chosen(): void
    {
        $staffMember = User::factory()->staff()->create();

        Livewire::actingAs(User::factory()->admin()->create())->test(Lookup::class, ['source' => 'customer'])
            ->call('select', '999999')->assertSet('value', '')
            ->call('select', (string) $staffMember->id)->assertSet('value', '')
            ->call('select', 'abc')->assertSet('value', '');
    }

    public function test_customers_cannot_search_customers_but_may_search_services_and_products(): void
    {
        $customer = User::factory()->customer()->create();
        Service::factory()->create(['name' => 'Electroplating']);

        Livewire::actingAs($customer)->test(Lookup::class, ['source' => 'customer'])->assertForbidden();
        Livewire::actingAs($customer)->test(Lookup::class, ['source' => 'service'])->call('openPanel')->assertSee('Electroplating');
    }

    public function test_an_unknown_source_is_rejected(): void
    {
        Livewire::actingAs(User::factory()->staff()->create())->test(Lookup::class, ['source' => 'users'])->assertNotFound();
    }

    public function test_the_forms_do_not_load_the_lists_with_the_page(): void
    {
        $staff = User::factory()->staff()->create();
        $chosen = User::factory()->customer()->create(['name' => 'Chosen Customer']);
        User::factory()->customer()->count(30)->create();
        Service::factory()->create(['name' => 'Unchosen Service']);

        $this->actingAs($staff)->get('/app/inquiries/create')
            ->assertOk()
            ->assertDontSee('Chosen Customer')
            ->assertDontSee('Unchosen Service');

        $this->actingAs($staff)->get('/app/inquiries')->assertOk()->assertDontSee('Unchosen Service');

        Livewire::actingAs($staff)->test(Editor::class)
            ->set('form.customerId', (string) $chosen->id)
            ->assertSee('Chosen Customer');
    }

    public function test_choosing_in_a_filter_narrows_the_list(): void
    {
        $alice = User::factory()->customer()->create(['name' => 'Alice Customer']);
        $bob = User::factory()->customer()->create(['name' => 'Bob Customer']);
        Inquiry::factory()->create(['customer_id' => $alice->id, 'question' => 'Alice question']);
        Inquiry::factory()->create(['customer_id' => $bob->id, 'question' => 'Bob question']);

        Livewire::withQueryParams(['customer' => (string) $bob->id])->actingAs(User::factory()->staff()->create())->test(Index::class)
            ->assertSee('Bob question')->assertDontSee('Alice question');
    }

    public function test_the_query_count_does_not_depend_on_how_many_records_exist(): void
    {
        $staff = User::factory()->staff()->create();
        User::factory()->customer()->count(3)->create();

        DB::enableQueryLog();
        Livewire::actingAs($staff)->test(Lookup::class, ['source' => 'customer'])->call('openPanel');
        $few = count(DB::getQueryLog());

        User::factory()->customer()->count(40)->create();
        DB::flushQueryLog();
        Livewire::actingAs($staff)->test(Lookup::class, ['source' => 'customer'])->call('openPanel');

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_the_texts_exist_in_both_dashboard_languages(): void
    {
        $en = (require lang_path('en/dashboard.php'))['lookup'];
        $id = (require lang_path('id/dashboard.php'))['lookup'];

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(['search', 'no_results', 'more', 'searching'], array_keys($en));
    }

    public function test_the_dropdown_markup_is_a_labelled_combobox_with_a_search_box(): void
    {
        $html = Livewire::actingAs(User::factory()->staff()->create())
            ->test(Lookup::class, ['source' => 'service', 'inputId' => 'service', 'placeholder' => 'All services'])
            ->html();

        $this->assertStringContainsString('role="combobox"', $html);
        $this->assertStringContainsString('aria-haspopup="listbox"', $html);
        $this->assertStringContainsString('role="listbox"', $html);
        $this->assertStringContainsString('wire:model.live.debounce.300ms="search"', $html);
        $this->assertStringContainsString('aria-label="'.__('dashboard.lookup.search').'"', $html);
    }
}

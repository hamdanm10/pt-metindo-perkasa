<?php

declare(strict_types=1);

namespace Tests\Feature\Whatsapp;

use App\Livewire\Whatsapp\Editor;
use App\Livewire\Whatsapp\Index;
use App\Models\User;
use App\Models\WhatsappAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsappAgentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_only_the_admin_reaches_the_pages(): void
    {
        $agent = WhatsappAgent::factory()->create();
        $urls = ['/app/whatsapp', '/app/whatsapp/create', "/app/whatsapp/{$agent->id}/edit"];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('app.login'));
        }

        foreach ($urls as $url) {
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
            $this->actingAs($this->admin())->get($url)->assertOk();
        }
    }

    public function test_a_number_is_saved_as_digits_only_and_goes_last_and_active(): void
    {
        WhatsappAgent::factory()->create(['position' => 4]);

        Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.name', '  Sales  ')
            ->set('form.number', '+62 812-0000-0001')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.whatsapp.index'));

        $agent = WhatsappAgent::query()->where('name', 'Sales')->firstOrFail();

        $this->assertSame('6281200000001', $agent->number);
        $this->assertSame(5, $agent->position);
        $this->assertTrue($agent->is_active);
    }

    public function test_the_name_and_a_complete_unique_number_are_required(): void
    {
        WhatsappAgent::factory()->create(['number' => '6281200000001']);

        $component = Livewire::actingAs($this->admin())->test(Editor::class);

        $component->call('save')->assertHasErrors(['form.name', 'form.number']);

        foreach (['abc', '12345', '0812000000', '1234567890123456', '+62 812-0000-0001'] as $bad) {
            $component->set('form.name', 'X')->set('form.number', $bad)->call('save')->assertHasErrors('form.number');
        }

        $this->assertSame(1, WhatsappAgent::query()->count());
    }

    public function test_editing_keeps_the_own_number_and_changes_the_name(): void
    {
        $agent = WhatsappAgent::factory()->create(['name' => 'Old', 'number' => '6281200000001']);

        Livewire::actingAs($this->admin())->test(Editor::class, ['agent' => $agent])
            ->assertSet('form.number', '+6281200000001')
            ->set('form.name', 'New')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New', $agent->refresh()->name);
        $this->assertSame('6281200000001', $agent->number);
    }

    public function test_numbers_move_up_and_down_and_turn_on_and_off(): void
    {
        $a = WhatsappAgent::factory()->create(['position' => 0]);
        $b = WhatsappAgent::factory()->create(['position' => 1]);
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Index::class)->call('move', $b->id, 'up');

        $this->assertSame(0, $b->refresh()->position);
        $this->assertSame(1, $a->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $b->id, 'up');
        $this->assertSame(0, $b->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('toggle', $b->id);
        $this->assertFalse($b->refresh()->is_active);

        Livewire::actingAs($admin)->test(Index::class)->call('toggle', $b->id);
        $this->assertTrue($b->refresh()->is_active);
    }

    public function test_deleting_removes_the_number(): void
    {
        $agent = WhatsappAgent::factory()->create();

        Livewire::actingAs($this->admin())->test(Index::class)
            ->call('delete', $agent->id)
            ->assertSee(__('whatsapp_manager.messages.deleted'));

        $this->assertSame(0, WhatsappAgent::query()->count());
    }

    public function test_staff_cannot_change_the_numbers_through_the_components(): void
    {
        $agent = WhatsappAgent::factory()->create();
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();

        $this->assertNotNull($agent->fresh());
    }

    public function test_the_landing_button_follows_what_the_admin_does(): void
    {
        $this->get('/en')->assertDontSee('data-widget-toggle="whatsapp"', false);

        $agent = WhatsappAgent::factory()->create(['name' => 'Sales', 'number' => '6281200000001']);
        $admin = $this->admin();

        $this->get('/en')->assertSee('data-widget-toggle="whatsapp"', false)->assertSee('https://wa.me/6281200000001?text=', false)->assertSee('Sales');

        Livewire::actingAs($admin)->test(Index::class)->call('toggle', $agent->id);
        $this->get('/en')->assertDontSee('data-widget-toggle="whatsapp"', false);

        Livewire::actingAs($admin)->test(Index::class)->call('toggle', $agent->id);
        $this->get('/id/contact')->assertSee('https://wa.me/6281200000001', false);

        Livewire::actingAs($admin)->test(Index::class)->call('delete', $agent->id);
        $this->get('/en')->assertDontSee('wa.me', false);
    }

    public function test_the_list_does_not_run_more_queries_when_there_are_more_numbers(): void
    {
        $admin = $this->admin();
        WhatsappAgent::factory()->create();

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            Livewire::actingAs($admin)->test(Index::class);

            return count(DB::getQueryLog());
        };

        $few = $count();
        WhatsappAgent::factory()->count(6)->create();

        $this->assertSame($few, $count());
    }

    public function test_the_menu_has_the_page_for_the_admin_only_and_the_texts_exist_in_both_languages(): void
    {
        $this->actingAs($this->admin())->get('/app/dashboard')->assertSee(route('app.whatsapp.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.whatsapp.index'), false);

        $this->assertSame(array_keys(trans('whatsapp_manager', [], 'en')), array_keys(trans('whatsapp_manager', [], 'id')));
        $this->assertNotSame('dashboard.nav.whatsapp', __('dashboard.nav.whatsapp', [], 'id'));

        foreach (config('locales.landing') as $code) {
            $this->assertArrayNotHasKey('default_name', trans('chat.whatsapp', [], $code));
        }
    }
}

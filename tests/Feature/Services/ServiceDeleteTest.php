<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Livewire\Services\Index;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_deletes_a_service_and_the_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('services/a.webp', 'x');
        $service = Service::factory()->create(['image_path' => 'services/a.webp']);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(Index::class)
            ->call('delete', $service->id)
            ->assertSee(__('services.messages.deleted'));

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        Storage::disk('public')->assertMissing('services/a.webp');
    }

    public function test_staff_cannot_delete_a_service(): void
    {
        $service = Service::factory()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->assertTrue(Gate::forUser(User::factory()->staff()->create())->denies('delete', $service));

        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    public function test_the_list_shows_a_delete_button_per_row_without_extra_queries(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->count(2)->create();

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $html = (string) $this->actingAs($admin)->get('/app/services')->getContent();
            $this->assertSame(substr_count($html, 'wire:key="service-'), substr_count($html, 'wire:click="delete('));

            return count(DB::getQueryLog());
        };

        $before = $count();
        Service::factory()->count(3)->create();

        $this->assertSame($before, $count());
    }
}

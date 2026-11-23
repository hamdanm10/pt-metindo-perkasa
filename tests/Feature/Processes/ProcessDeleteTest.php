<?php

declare(strict_types=1);

namespace Tests\Feature\Processes;

use App\Livewire\Processes\Index;
use App\Models\Process;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class ProcessDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_deletes_a_process_and_the_files(): void
    {
        $process = Process::factory()->create([]);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(Index::class)
            ->call('delete', $process->id)
            ->assertSee(__('processes.messages.deleted'));

        $this->assertDatabaseMissing('processes', ['id' => $process->id]);
    }

    public function test_staff_cannot_delete_a_process(): void
    {
        $process = Process::factory()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->assertTrue(Gate::forUser(User::factory()->staff()->create())->denies('delete', $process));

        $this->assertDatabaseHas('processes', ['id' => $process->id]);
    }

    public function test_the_list_shows_a_delete_button_per_row_without_extra_queries(): void
    {
        $admin = User::factory()->admin()->create();
        Process::factory()->count(2)->create();

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $html = (string) $this->actingAs($admin)->get('/app/processes')->getContent();
            $this->assertSame(substr_count($html, 'wire:key="process-'), substr_count($html, 'wire:click="delete('));

            return count(DB::getQueryLog());
        };

        $before = $count();
        Process::factory()->count(3)->create();

        $this->assertSame($before, $count());
    }
}

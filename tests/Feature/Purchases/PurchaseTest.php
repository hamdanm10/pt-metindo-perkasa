<?php

declare(strict_types=1);

namespace Tests\Feature\Purchases;

use App\Livewire\Purchases\Editor;
use App\Livewire\Purchases\Index;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseFile;
use App\Models\User;
use App\Services\Dashboard\DashboardMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
    }

    private function pdf(string $name = 'invoice.pdf', int $kilobytes = 20): UploadedFile
    {
        return UploadedFile::fake()->create($name, $kilobytes, 'application/pdf');
    }

    private function withFile(Purchase $purchase, string $name = 'invoice.pdf'): PurchaseFile
    {
        $path = 'purchases/'.fake()->uuid().'.pdf';
        Storage::disk('local')->put($path, 'pdf-bytes');

        return $purchase->files()->create(['path' => $path, 'name' => $name, 'mime_type' => 'application/pdf', 'size' => 9]);
    }

    /**
     * Fills the form of a new purchase with one product row.
     *
     * @param  Testable<Component>  $component
     * @return Testable<Component>
     */
    private function fill(Testable $component, ?User $buyer = null, ?Product $product = null): Testable
    {
        return $component
            ->set('form.buyerId', (string) ($buyer ?? User::factory()->customer()->create())->id)
            ->set('form.purchasedAt', '2026-10-01')
            ->set('form.items.0.productId', (string) ($product ?? Product::factory()->create())->id)
            ->set('form.items.0.quantity', '5');
    }

    public function test_access_by_role(): void
    {
        $purchase = Purchase::factory()->create();

        $this->get('/app/customer-purchases')->assertRedirect(route('app.login'));
        $this->get("/app/purchase-files/{$this->withFile($purchase)->id}")->assertRedirect(route('app.login'));

        foreach (['admin', 'staff'] as $role) {
            $user = User::factory()->{$role}()->create();
            $this->actingAs($user)->get('/app/customer-purchases')->assertOk();
            $this->actingAs($user)->get("/app/customer-purchases/{$purchase->id}")->assertOk();
            $this->actingAs($user)->get('/app/customer-purchases/create')->assertOk();
        }

        foreach (['customer', 'distributor'] as $role) {
            $user = User::factory()->{$role}()->create();
            $this->actingAs($user)->get('/app/customer-purchases')->assertForbidden();
            $this->actingAs($user)->get("/app/customer-purchases/{$purchase->id}")->assertForbidden();
            $this->actingAs($user)->get('/app/customer-purchases/create')->assertForbidden();
            $this->actingAs($user)->get("/app/customer-purchases/{$purchase->id}/edit")->assertForbidden();
        }
    }

    public function test_the_policy_lets_staff_read_all_and_only_the_creator_or_admin_change(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $otherStaff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();
        $mine = Purchase::factory()->create(['created_by' => $staff->id]);
        $orphan = Purchase::factory()->create(['created_by' => null]);

        foreach ([$admin, $staff, $otherStaff] as $user) {
            $this->assertTrue(Gate::forUser($user)->allows('viewAny', Purchase::class));
            $this->assertTrue(Gate::forUser($user)->allows('view', $mine));
            $this->assertTrue(Gate::forUser($user)->allows('create', Purchase::class));
        }

        $this->assertTrue(Gate::forUser($customer)->allows('viewAny', Purchase::class));
        $this->assertFalse(Gate::forUser($customer)->allows('create', Purchase::class));
        $this->assertFalse(Gate::forUser($customer)->allows('view', $mine));
        $this->assertTrue(Gate::forUser($customer)->allows('view', Purchase::factory()->create(['buyer_id' => $customer->id])));

        foreach (['update', 'delete'] as $ability) {
            $this->assertTrue(Gate::forUser($admin)->allows($ability, $mine));
            $this->assertTrue(Gate::forUser($admin)->allows($ability, $orphan));
            $this->assertTrue(Gate::forUser($staff)->allows($ability, $mine));
            $this->assertFalse(Gate::forUser($otherStaff)->allows($ability, $mine));
            $this->assertFalse(Gate::forUser($staff)->allows($ability, $orphan));
            $this->assertFalse(Gate::forUser($customer)->allows($ability, $mine));
        }
    }

    public function test_staff_creates_a_purchase_for_a_customer_with_products_and_is_its_author(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();
        $first = Product::factory()->create(['name' => 'Zinc Brightener']);
        $second = Product::factory()->create(['name' => 'Nickel Salt']);

        $component = Livewire::actingAs($staff)->test(Editor::class, ['kind' => 'customer']);
        $this->fill($component, $customer, $first)
            ->set('form.invoiceNumber', ' INV-001 ')
            ->set('form.notes', ' Paid in cash ')
            ->call('addItem')
            ->set('form.items.1.productId', (string) $second->id)
            ->set('form.items.1.quantity', '12')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.customer-purchases.index'));

        $purchase = Purchase::query()->with('items')->firstOrFail();
        $this->assertSame($customer->id, $purchase->buyer_id);
        $this->assertSame($staff->id, $purchase->created_by);
        $this->assertSame('2026-10-01', $purchase->purchased_at->toDateString());
        $this->assertSame('INV-001', $purchase->invoice_number);
        $this->assertSame('Paid in cash', $purchase->notes);
        $this->assertSame([['Zinc Brightener', 5], ['Nickel Salt', 12]], $purchase->items->map(fn ($item) => [$item->product_name, $item->quantity])->all());
    }

    public function test_a_distributor_purchase_is_made_on_the_distributor_page(): void
    {
        $distributor = User::factory()->distributor()->create();

        $this->fill(Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['kind' => 'distributor']), $distributor)
            ->call('save')->assertHasNoErrors()
            ->assertRedirect(route('app.distributor-purchases.index'));

        $this->assertSame($distributor->id, Purchase::query()->firstOrFail()->buyer_id);
    }

    public function test_the_fields_are_validated(): void
    {
        $component = Livewire::actingAs(User::factory()->staff()->create())->test(Editor::class, ['kind' => 'customer']);

        $component->set('form.purchasedAt', '')->set('form.items.0.quantity', '')
            ->call('save')
            ->assertHasErrors(['form.buyerId', 'form.purchasedAt', 'form.items.0.productId', 'form.items.0.quantity']);

        $this->fill($component)
            ->set('form.purchasedAt', now()->addDay()->toDateString())->call('save')->assertHasErrors(['form.purchasedAt']);

        $this->fill($component)
            ->set('form.invoiceNumber', str_repeat('a', 101))->call('save')->assertHasErrors(['form.invoiceNumber']);

        $this->fill($component)
            ->set('form.notes', str_repeat('a', 2001))->call('save')->assertHasErrors(['form.notes']);

        $this->assertSame(0, Purchase::query()->count());
    }

    public function test_the_buyer_must_have_the_role_of_the_page(): void
    {
        $admin = User::factory()->admin()->create();
        $customerPage = Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer']);
        $distributorPage = Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'distributor']);

        foreach ([User::factory()->staff()->create()->id, User::factory()->admin()->create()->id, User::factory()->distributor()->create()->id, 999999] as $id) {
            $this->fill($customerPage)->set('form.buyerId', (string) $id)->call('save')->assertHasErrors(['form.buyerId']);
        }

        $this->fill($distributorPage)->set('form.buyerId', (string) User::factory()->customer()->create()->id)->call('save')->assertHasErrors(['form.buyerId']);

        $this->assertSame(0, Purchase::query()->count());
    }

    public function test_customer_and_distributor_purchases_are_on_separate_pages(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create(['name' => 'Cara Customer']);
        $distributor = User::factory()->distributor()->create(['name' => 'Dion Distributor']);
        $forCustomer = Purchase::factory()->create(['buyer_id' => $customer->id]);
        $forDistributor = Purchase::factory()->create(['buyer_id' => $distributor->id]);

        $this->actingAs($staff)->get('/app/customer-purchases')->assertOk()->assertSeeText('Cara Customer')->assertDontSeeText('Dion Distributor');
        $this->actingAs($staff)->get('/app/distributor-purchases')->assertOk()->assertSeeText('Dion Distributor')->assertDontSeeText('Cara Customer');

        $this->actingAs($staff)->get("/app/customer-purchases/{$forCustomer->id}")->assertOk();
        $this->actingAs($staff)->get("/app/distributor-purchases/{$forDistributor->id}")->assertOk();
        $this->actingAs($staff)->get("/app/customer-purchases/{$forDistributor->id}")->assertNotFound();
        $this->actingAs($staff)->get("/app/distributor-purchases/{$forCustomer->id}")->assertNotFound();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get("/app/customer-purchases/{$forDistributor->id}/edit")->assertNotFound();
        $this->actingAs($admin)->get("/app/distributor-purchases/{$forDistributor->id}/edit")->assertOk();
        $this->actingAs($admin)->get('/app/distributor-purchases/create')->assertOk();
        $this->actingAs($admin)->get('/app/purchases')->assertNotFound();
    }

    public function test_product_rows_are_checked(): void
    {
        $component = $this->fill(Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['kind' => 'customer']));
        $product = Product::factory()->create();

        $component->set('form.items.0.productId', (string) $product->id)
            ->call('addItem')->set('form.items.1.productId', (string) $product->id)
            ->call('save')->assertHasErrors(['form.items.0.productId', 'form.items.1.productId']);

        $component->set('form.items.1.productId', '999999')->call('save')->assertHasErrors(['form.items.1.productId']);

        $component->call('removeItem', 1)->set('form.items.0.quantity', '0')->call('save')->assertHasErrors(['form.items.0.quantity']);
        $component->set('form.items.0.quantity', '1.5')->call('save')->assertHasErrors(['form.items.0.quantity']);

        $component->set('form.items', [])->call('save')->assertHasErrors(['form.items']);

        $this->assertSame(0, Purchase::query()->count());
    }

    public function test_rows_cannot_exceed_the_maximum(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['kind' => 'customer']);

        for ($i = 0; $i < Purchase::MAX_ITEMS + 5; $i++) {
            $component->call('addItem');
        }

        $this->assertCount(Purchase::MAX_ITEMS, $component->get('form.items'));
    }

    public function test_editing_replaces_the_product_rows_and_keeps_the_author(): void
    {
        $staff = User::factory()->staff()->create();
        $purchase = Purchase::factory()->create(['created_by' => $staff->id]);
        $old = Product::factory()->create(['name' => 'Old']);
        $purchase->items()->create(['product_id' => $old->id, 'product_name' => 'Old', 'quantity' => 1]);
        $new = Product::factory()->create(['name' => 'New']);
        $buyer = User::factory()->customer()->create();

        Livewire::actingAs($staff)->test(Editor::class, ['kind' => 'customer', 'purchase' => $purchase])
            ->assertSet('form.items.0.productId', (string) $old->id)
            ->set('form.items.0.productId', (string) $new->id)
            ->set('form.items.0.quantity', '3')
            ->set('form.buyerId', (string) $buyer->id)
            ->call('save')->assertHasNoErrors();

        $purchase->refresh()->load('items');
        $this->assertSame($buyer->id, $purchase->buyer_id);
        $this->assertSame($staff->id, $purchase->created_by);
        $this->assertSame([['New', 3]], $purchase->items->map(fn ($item) => [$item->product_name, $item->quantity])->all());
    }

    public function test_staff_edits_only_their_own_purchases_and_the_admin_edits_any(): void
    {
        $staff = User::factory()->staff()->create();
        $admin = User::factory()->admin()->create();
        $own = Purchase::factory()->create(['created_by' => $staff->id]);
        $others = Purchase::factory()->create(['created_by' => User::factory()->staff()->create()->id]);
        $orphan = Purchase::factory()->create(['created_by' => null]);

        $this->actingAs($staff)->get("/app/customer-purchases/{$own->id}/edit")->assertOk();
        $this->actingAs($staff)->get("/app/customer-purchases/{$others->id}/edit")->assertForbidden();
        $this->actingAs($staff)->get("/app/customer-purchases/{$orphan->id}/edit")->assertForbidden();
        $this->actingAs($staff)->get("/app/customer-purchases/{$others->id}")->assertOk();

        $this->actingAs($admin)->get("/app/customer-purchases/{$others->id}/edit")->assertOk();
        $this->actingAs($admin)->get("/app/customer-purchases/{$orphan->id}/edit")->assertOk();
    }

    public function test_staff_deletes_only_their_own_purchases_and_the_files_go_with_them(): void
    {
        $staff = User::factory()->staff()->create();
        $own = Purchase::factory()->create(['created_by' => $staff->id]);
        $file = $this->withFile($own);
        $others = Purchase::factory()->create(['created_by' => User::factory()->staff()->create()->id]);
        $othersFile = $this->withFile($others);

        Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->call('delete', $others->id)->assertForbidden();
        $this->assertModelExists($others);
        Storage::disk('local')->assertExists($othersFile->path);

        Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->call('delete', $own->id)->assertOk();
        $this->assertModelMissing($own);
        $this->assertDatabaseMissing('purchase_files', ['id' => $file->id]);
        Storage::disk('local')->assertMissing($file->path);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class, ['kind' => 'customer'])->call('delete', $others->id);
        $this->assertModelMissing($others);
        Storage::disk('local')->assertMissing($othersFile->path);
    }

    public function test_files_are_stored_privately_with_random_names(): void
    {
        $component = $this->fill(Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['kind' => 'customer']))
            ->set('uploads', [$this->pdf('secret-invoice.pdf'), UploadedFile::fake()->image('proof.png')]);

        $component->assertHasNoErrors()->call('save')->assertHasNoErrors();

        $purchase = Purchase::query()->with('files')->firstOrFail();
        $this->assertCount(2, $purchase->files);

        foreach ($purchase->files as $file) {
            $this->assertStringStartsWith('purchases/', $file->path);
            $this->assertStringNotContainsString('secret-invoice', $file->path);
            Storage::disk('local')->assertExists($file->path);
        }

        $this->assertSame(['secret-invoice.pdf', 'proof.png'], $purchase->files->pluck('name')->all());
        $this->assertSame('application/pdf', $purchase->files->firstOrFail()->mime_type);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_file_type_and_size_are_checked(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['kind' => 'customer']);

        $component->set('uploads', [UploadedFile::fake()->create('macro.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')])
            ->assertHasErrors(['uploads.0']);
        $component->set('uploads', [UploadedFile::fake()->create('script.php', 1, 'text/x-php')])
            ->assertHasErrors(['uploads.0']);
        $component->set('uploads', [$this->pdf('big.pdf', 5121)])->assertHasErrors(['uploads.0']);
        $component->set('uploads', [$this->pdf('ok.pdf', 5120)])->assertHasNoErrors();
    }

    public function test_at_most_ten_files_per_purchase_counting_saved_ones(): void
    {
        $admin = User::factory()->admin()->create();
        $component = Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer']);

        $component->set('uploads', array_map(fn (int $i) => $this->pdf("f{$i}.pdf"), range(1, 11)))
            ->assertHasErrors(['form.files']);
        $this->assertCount(0, $component->get('form.files'));

        $component->set('uploads', array_map(fn (int $i) => $this->pdf("f{$i}.pdf"), range(1, 10)))->assertHasNoErrors();
        $this->assertCount(10, $component->get('form.files'));

        $purchase = Purchase::factory()->create(['created_by' => $admin->id]);
        $saved = $this->withFile($purchase);
        $this->withFile($purchase);

        $edit = Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer', 'purchase' => $purchase]);
        $edit->set('uploads', array_map(fn (int $i) => $this->pdf("n{$i}.pdf"), range(1, 9)))->assertHasErrors(['form.files']);

        $edit->call('toggleRemoveFile', $saved->id)
            ->set('uploads', array_map(fn (int $i) => $this->pdf("n{$i}.pdf"), range(1, 9)))->assertHasNoErrors();
    }

    public function test_a_waiting_file_can_be_taken_back_and_removal_can_be_undone(): void
    {
        $admin = User::factory()->admin()->create();
        $purchase = Purchase::factory()->create(['created_by' => $admin->id]);
        $file = $this->withFile($purchase);
        $product = Product::factory()->create();
        $purchase->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1]);

        $component = Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer', 'purchase' => $purchase])
            ->set('uploads', [$this->pdf('a.pdf'), $this->pdf('b.pdf')])
            ->call('removePending', 0);
        $this->assertCount(1, $component->get('form.files'));

        $component->call('toggleRemoveFile', $file->id)->assertSet('form.removedFileIds', [$file->id])
            ->call('toggleRemoveFile', $file->id)->assertSet('form.removedFileIds', [])
            ->call('save')->assertHasNoErrors();

        $this->assertSame(2, $purchase->files()->count());
        Storage::disk('local')->assertExists($file->path);
    }

    public function test_removing_a_file_deletes_it_from_the_disk_and_ignores_files_of_other_purchases(): void
    {
        $admin = User::factory()->admin()->create();
        $purchase = Purchase::factory()->create(['created_by' => $admin->id]);
        $product = Product::factory()->create();
        $purchase->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1]);
        $mine = $this->withFile($purchase);
        $foreign = $this->withFile(Purchase::factory()->create());

        Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer', 'purchase' => $purchase])
            ->call('toggleRemoveFile', $mine->id)
            ->call('toggleRemoveFile', $foreign->id)
            ->call('save')->assertHasNoErrors();

        $this->assertDatabaseMissing('purchase_files', ['id' => $mine->id]);
        Storage::disk('local')->assertMissing($mine->path);
        $this->assertDatabaseHas('purchase_files', ['id' => $foreign->id]);
        Storage::disk('local')->assertExists($foreign->path);
    }

    public function test_the_show_page_lists_the_products_and_the_files(): void
    {
        $buyer = User::factory()->customer()->create();
        $purchase = Purchase::factory()->create(['buyer_id' => $buyer->id, 'invoice_number' => 'INV-77', 'notes' => 'Deliver on Monday']);
        $purchase->items()->create(['product_id' => null, 'product_name' => 'Zinc Brightener', 'quantity' => 1500]);
        $file = $this->withFile($purchase, 'faktur.pdf');

        $this->actingAs(User::factory()->staff()->create())->get("/app/customer-purchases/{$purchase->id}")
            ->assertOk()
            ->assertSeeText($buyer->name)
            ->assertSeeText('INV-77')
            ->assertSeeText('Deliver on Monday')
            ->assertSeeText('Zinc Brightener')
            ->assertSeeText('1,500')
            ->assertSeeText('faktur.pdf')
            ->assertSee(route('app.purchase-files.show', $file), false);
    }

    public function test_files_are_downloaded_by_admin_and_staff_only(): void
    {
        $purchase = Purchase::factory()->create();
        $file = $this->withFile($purchase, 'faktur.pdf');

        foreach (['admin', 'staff'] as $role) {
            $response = $this->actingAs(User::factory()->{$role}()->create())->get("/app/purchase-files/{$file->id}");
            $response->assertOk()->assertDownload('faktur.pdf');
            $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        }

        $this->actingAs(User::factory()->customer()->create())->get("/app/purchase-files/{$file->id}")->assertForbidden();
        $this->actingAs(User::factory()->staff()->create())->get('/app/purchase-files/999999')->assertNotFound();
    }

    public function test_search_filter_and_order(): void
    {
        $staff = User::factory()->staff()->create();
        $alice = User::factory()->customer()->create(['name' => 'Alice Plating']);
        $bob = User::factory()->customer()->create(['name' => 'Bob Supply']);
        $old = Purchase::factory()->create(['buyer_id' => $alice->id, 'invoice_number' => 'INV-100', 'purchased_at' => '2026-01-01']);
        $new = Purchase::factory()->create(['buyer_id' => $bob->id, 'invoice_number' => 'INV-200', 'purchased_at' => '2026-06-01']);

        $names = fn ($component) => $component->viewData('purchases')->pluck('id')->all();

        $this->assertSame([$new->id, $old->id], $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])));
        $this->assertSame([$old->id], $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->set('search', 'inv-100')));
        $this->assertSame([$old->id], $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->set('search', 'alice')));
        $this->assertSame([$new->id], $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->set('buyer', (string) $bob->id)));
        $this->assertSame([], $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->set('search', 'nothing')));
        $both = $names(Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer'])->set('search', 'INV'));
        sort($both);
        $this->assertSame([$old->id, $new->id], $both);
    }

    public function test_deleting_a_product_keeps_the_purchase_and_the_saved_name(): void
    {
        $purchase = Purchase::factory()->create();
        $product = Product::factory()->create(['name' => 'Gone Soon']);
        $purchase->items()->create(['product_id' => $product->id, 'product_name' => 'Gone Soon', 'quantity' => 2]);

        $product->delete();

        $item = $purchase->items()->firstOrFail();
        $this->assertNull($item->product_id);
        $this->assertSame('Gone Soon', $item->product_name);
        $this->actingAs(User::factory()->admin()->create())->get("/app/customer-purchases/{$purchase->id}")->assertOk()->assertSeeText('Gone Soon');
    }

    public function test_editing_a_purchase_whose_product_was_deleted_asks_for_another_product(): void
    {
        $admin = User::factory()->admin()->create();
        $purchase = Purchase::factory()->create(['created_by' => $admin->id]);
        $purchase->items()->create(['product_id' => null, 'product_name' => 'Gone', 'quantity' => 2]);

        Livewire::actingAs($admin)->test(Editor::class, ['kind' => 'customer', 'purchase' => $purchase])
            ->assertSet('form.items.0.productId', '')
            ->assertSeeText('"Gone"')
            ->call('save')->assertHasErrors(['form.items.0.productId']);
    }

    public function test_the_list_query_count_does_not_grow_with_rows(): void
    {
        $staff = User::factory()->staff()->create();
        $make = function (int $count) use ($staff): void {
            Purchase::factory()->count($count)->create(['created_by' => $staff->id])->each(function (Purchase $purchase): void {
                $purchase->items()->create(['product_id' => null, 'product_name' => 'P', 'quantity' => 1]);
                $this->withFile($purchase);
            });
        };

        $make(2);
        DB::enableQueryLog();
        Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer']);
        $few = count(DB::getQueryLog());

        $make(8);
        DB::flushQueryLog();
        Livewire::actingAs($staff)->test(Index::class, ['kind' => 'customer']);

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_customers_and_distributors_only_read_their_own_purchases(): void
    {
        $this->get('/customer/purchases')->assertRedirect(route('portal.login'));

        foreach (['customer', 'distributor'] as $role) {
            $buyer = User::factory()->{$role}()->create();
            $own = Purchase::factory()->create(['buyer_id' => $buyer->id, 'invoice_number' => 'OWN-1', 'created_by' => User::factory()->staff()->create(['name' => 'Secret Staffer'])->id]);
            $product = Product::factory()->create();
            $own->items()->create(['product_id' => $product->id, 'product_name' => 'Own Product', 'quantity' => 4]);
            $file = $this->withFile($own, 'own-invoice.pdf');
            $other = Purchase::factory()->create(['buyer_id' => User::factory()->{$role}()->create()->id, 'invoice_number' => 'OTHER-1']);
            $otherFile = $this->withFile($other);

            $this->actingAs($buyer)->get("/{$role}/purchases")->assertOk()->assertSeeText('OWN-1')->assertDontSeeText('OTHER-1')->assertDontSeeText('Secret Staffer');
            $this->actingAs($buyer)->get("/{$role}/purchases/{$own->id}")->assertOk()->assertSeeText('Own Product')->assertSeeText('own-invoice.pdf')->assertDontSeeText('Secret Staffer')
                ->assertSee(route("{$role}.purchase-files.show", $file), false)->assertDontSee('/edit', false);
            $this->actingAs($buyer)->get("/{$role}/purchases/{$other->id}")->assertNotFound();

            $this->actingAs($buyer)->get("/{$role}/purchase-files/{$file->id}")->assertOk()->assertDownload('own-invoice.pdf');
            $this->actingAs($buyer)->get("/{$role}/purchase-files/{$otherFile->id}")->assertNotFound();

            $this->actingAs($buyer)->get('/app/customer-purchases')->assertForbidden();
            $this->actingAs($buyer)->get('/app/purchase-files/'.$file->id)->assertForbidden();
            Livewire::actingAs($buyer)->test(Index::class)->assertSeeText('OWN-1')->assertDontSeeText('OTHER-1')->set('buyer', (string) $other->buyer_id)->assertSeeText('OWN-1')->assertDontSeeText('OTHER-1');
        }

        $customer = User::factory()->customer()->create();
        $this->actingAs($customer)->get('/distributor/purchases')->assertForbidden();
        $this->actingAs(User::factory()->staff()->create())->get('/customer/purchases')->assertForbidden();
    }

    public function test_a_buyer_cannot_change_or_delete_purchases(): void
    {
        $buyer = User::factory()->customer()->create();
        $own = Purchase::factory()->create(['buyer_id' => $buyer->id]);

        $this->actingAs($buyer)->get('/app/customer-purchases/create')->assertForbidden();
        $this->actingAs($buyer)->get("/app/customer-purchases/{$own->id}/edit")->assertForbidden();
        Livewire::actingAs($buyer)->test(Index::class)->call('delete', $own->id)->assertForbidden();
        $this->assertModelExists($own);
    }

    public function test_the_buyer_list_query_count_does_not_grow_with_rows(): void
    {
        $buyer = User::factory()->customer()->create();
        $make = function (int $count) use ($buyer): void {
            Purchase::factory()->count($count)->create(['buyer_id' => $buyer->id])->each(function (Purchase $purchase): void {
                $purchase->items()->create(['product_id' => null, 'product_name' => 'P', 'quantity' => 1]);
                $this->withFile($purchase);
            });
        };

        $make(2);
        DB::enableQueryLog();
        Livewire::actingAs($buyer)->test(Index::class);
        $few = count(DB::getQueryLog());

        $make(8);
        DB::flushQueryLog();
        Livewire::actingAs($buyer)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_the_menu_links_both_pages_for_admin_and_staff_only(): void
    {
        $menu = new DashboardMenu;

        foreach (['app.customer-purchases.index', 'app.distributor-purchases.index'] as $route) {
            $this->assertContains($route, array_column($menu->forUser(User::factory()->admin()->create()), 'route'));
            $this->assertContains($route, array_column($menu->forUser(User::factory()->staff()->create()), 'route'));
            $this->assertNotContains($route, array_column($menu->forUser(User::factory()->customer()->create()), 'route'));
            $this->assertNotContains($route, array_column($menu->forUser(User::factory()->distributor()->create()), 'route'));
        }

        $this->assertContains('customer.purchases.index', array_column($menu->forUser(User::factory()->customer()->create()), 'route'));
        $this->assertContains('distributor.purchases.index', array_column($menu->forUser(User::factory()->distributor()->create()), 'route'));
    }

    public function test_the_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/purchases.php');
        $id = require lang_path('id/purchases.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertArrayHasKey('purchases', (require lang_path('id/dashboard.php'))['nav']);
        $this->assertSame(array_keys($en['kinds']['customer']), array_keys($id['kinds']['customer']));
        $this->assertSame(array_keys($en['kinds']['customer']), array_keys($en['kinds']['distributor']));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
        $this->assertArrayHasKey('customer_purchases', (require lang_path('en/dashboard.php'))['nav']);
        $this->assertArrayHasKey('distributor_purchases', (require lang_path('en/dashboard.php'))['nav']);
        $this->assertArrayHasKey('customer_purchases', (require lang_path('id/dashboard.php'))['nav']);
        $this->assertArrayHasKey('distributor_purchases', (require lang_path('id/dashboard.php'))['nav']);
        $this->assertArrayHasKey('sales', (require lang_path('en/dashboard.php'))['groups']);
        $this->assertArrayHasKey('sales', (require lang_path('id/dashboard.php'))['groups']);

        app()->setLocale('id');
        $this->assertSame('Catatan Pembelian Customer', __('purchases.kinds.customer.title'));
    }
}

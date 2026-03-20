<?php

namespace Tests\Feature\Filament;

use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'inv-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Facture',
            'email' => 'inv-mem-' . uniqid() . '@test.ch',
        ]);
    }

    public function test_invoice_list_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices')
            ->assertStatus(200);
    }

    public function test_invoice_list_shows_invoices(): void
    {
        $member = $this->makeMember();
        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => '2026-001-001',
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices')
            ->assertSee('2026-001-001');
    }

    public function test_invoice_navigation_exists(): void
    {
        $response = $this->actingAs($this->makeAdmin())
            ->get('/admin/invoices');

        $response->assertStatus(200);
        $response->assertSee('Factures');
    }
}

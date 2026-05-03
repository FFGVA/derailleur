<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class MaxParticipantsValidationTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::create(['name' => 'A', 'email' => 'mp-' . uniqid() . '@x.ch', 'password' => bcrypt('x'), 'role' => 'A']);
    }

    public function test_create_accepts_max_participants_15(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'P',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'max_participants' => '15',
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_create_rejects_more_than_four_digits(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'P',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'max_participants' => '12345',
            ])
            ->call('create')
            ->assertHasFormErrors(['max_participants']);
    }

    public function test_create_accepts_empty_max_participants(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'P',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'max_participants' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_edit_accepts_max_participants_15(): void
    {
        $event = Event::create(['title' => 'E', 'starts_at' => now()->addDay(), 'statuscode' => 'P', 'price' => 0]);

        Livewire::actingAs($this->admin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['max_participants' => '15'])
            ->call('save')
            ->assertHasNoFormErrors();
    }
}

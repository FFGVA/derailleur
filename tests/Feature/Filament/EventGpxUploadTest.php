<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EventGpxUploadTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'gpx-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Sortie Test GPX',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'N',
        ], $overrides));
    }

    public function test_gpx_file_field_exists_on_edit_form(): void
    {
        $admin = $this->makeAdmin();
        $event = $this->makeEvent();

        $this->actingAs($admin);

        Livewire::test(EditEvent::class, ['record' => $event->id])
            ->assertFormFieldExists('gpx_file');
    }

    public function test_can_upload_gpx_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $event = $this->makeEvent();

        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('parcours.gpx', 100, 'application/xml');

        Livewire::test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['gpx_file' => $file])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertNotNull($event->gpx_file);
    }

    public function test_accepts_xml_mime_type(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $event = $this->makeEvent();

        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('parcours.gpx', 100, 'text/xml');

        Livewire::test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['gpx_file' => $file])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertNotNull($event->gpx_file);
    }

    public function test_can_remove_gpx_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $event = $this->makeEvent(['gpx_file' => 'gpx/old-file.gpx']);

        $this->actingAs($admin);

        Livewire::test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['gpx_file' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertNull($event->gpx_file);
    }

    public function test_gpx_file_shown_on_view_page(): void
    {
        $admin = $this->makeAdmin();
        $event = $this->makeEvent(['gpx_file' => 'gpx/parcours.gpx']);

        $this->actingAs($admin);

        $response = $this->get(EventResource::getUrl('view', ['record' => $event]));
        $response->assertOk();
    }
}

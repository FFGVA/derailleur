<?php

namespace Tests\Unit\Services;

use App\Enums\EventMemberStatus;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Services\ExcelExportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExcelExportServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_export_participants_returns_file_path(): void
    {
        $event = Event::create([
            'title' => 'Export Test',
            'starts_at' => '2026-05-01 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $member = Member::create([
            'first_name' => 'Marie',
            'last_name' => 'Export',
            'email' => 'export-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => EventMemberStatus::Confirme->value,
        ]);

        $result = ExcelExportService::exportParticipants($event);

        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertFileExists($result['path']);
        $this->assertStringEndsWith('.xlsx', $result['filename']);

        unlink($result['path']);
    }

    public function test_export_filename_contains_event_slug(): void
    {
        $event = Event::create([
            'title' => 'Sortie Jura',
            'starts_at' => '2026-05-01 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $result = ExcelExportService::exportParticipants($event);

        $this->assertStringContainsString('sortie-jura', $result['filename']);

        unlink($result['path']);
    }
}

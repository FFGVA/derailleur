<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Services\MemberCardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberCardServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_generate_returns_valid_pdf(): void
    {
        $member = Member::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'carte-test-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        $pdf = MemberCardService::generate($member);

        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertGreaterThan(1000, strlen($pdf));
    }

    public function test_generate_has_two_pages(): void
    {
        $member = Member::create([
            'first_name' => 'Sophie',
            'last_name' => 'Martin',
            'email' => 'carte-test-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        $pdf = MemberCardService::generate($member);

        // PDF should reference 2 pages
        $this->assertStringContainsString('/Count 2', $pdf);
    }

    public function test_filename_includes_member_name(): void
    {
        $member = new Member(['first_name' => 'Julie', 'last_name' => 'Bernard']);

        $filename = MemberCardService::filename($member);

        $this->assertEquals('FFGVA - Membre Julie Bernard.pdf', $filename);
    }
}

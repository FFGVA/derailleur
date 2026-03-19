<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_all_cases_exist(): void
    {
        $cases = UserRole::cases();

        $this->assertCount(2, $cases);
        $this->assertNotNull(UserRole::from('A'));
        $this->assertNotNull(UserRole::from('C'));
    }

    public function test_case_values(): void
    {
        $this->assertSame('A', UserRole::Admin->value);
        $this->assertSame('C', UserRole::ChefPeloton->value);
    }

    public function test_get_label_returns_french_labels(): void
    {
        $this->assertSame('Administratrice', UserRole::Admin->getLabel());
        $this->assertSame('Cheffe de peloton', UserRole::ChefPeloton->getLabel());
    }
}

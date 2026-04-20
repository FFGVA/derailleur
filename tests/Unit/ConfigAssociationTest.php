<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigAssociationTest extends TestCase
{
    public function test_association_config_exists(): void
    {
        $this->assertNotNull(config('association.name'));
        $this->assertNotNull(config('association.iban'));
        $this->assertNotNull(config('association.mail_from_address'));
        $this->assertNotNull(config('association.mail_reply_to_address'));
        $this->assertNotNull(config('association.contact_email'));
        $this->assertNotNull(config('association.website_url'));
        $this->assertNotNull(config('association.logo_path'));
        $this->assertNotNull(config('association.currency'));
    }

    public function test_pdf_brand_color_is_rgb_array(): void
    {
        $rgb = config('association.colors.pdf_brand_rgb');
        $this->assertIsArray($rgb);
        $this->assertCount(3, $rgb);
        foreach ($rgb as $component) {
            $this->assertIsInt($component);
            $this->assertGreaterThanOrEqual(0, $component);
            $this->assertLessThanOrEqual(255, $component);
        }
    }

    public function test_all_pdf_color_configs_exist(): void
    {
        $this->assertNotNull(config('association.colors.pdf_brand_rgb'));
        $this->assertNotNull(config('association.colors.pdf_text_dark_rgb'));
        $this->assertNotNull(config('association.colors.pdf_text_light_rgb'));
        $this->assertNotNull(config('association.colors.pdf_separator_rgb'));
    }
}

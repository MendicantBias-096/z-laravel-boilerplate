<?php

namespace Tests\Unit;

use App\Enums\Language;
use App\Enums\Roles;
use Tests\TestCase;

class RolesEnumTest extends TestCase
{
    public function test_admin_has_correct_value(): void
    {
        $this->assertEquals('admin', Roles::ADMIN->value);
    }

    public function test_user_has_correct_value(): void
    {
        $this->assertEquals('user', Roles::USER->value);
    }

    public function test_values_returns_all_role_strings(): void
    {
        $values = Roles::values();

        $this->assertContains('admin', $values);
        $this->assertContains('user', $values);
        $this->assertCount(2, $values);
    }

    public function test_language_values_returns_supported_locales(): void
    {
        $values = Language::values();

        $this->assertContains('es', $values);
        $this->assertContains('en', $values);
    }

    public function test_language_options_returns_label_value_pairs(): void
    {
        $options = Language::options();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }
}

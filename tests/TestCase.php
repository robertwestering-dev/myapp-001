<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyFeature(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    /** Simulate that the acting user has recently confirmed their password. */
    protected function withPasswordConfirmed(): static
    {
        return $this->withSession(['auth.password_confirmed_at' => time()]);
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Boots the application.
     */
    use CreatesApplication; // O primeiro trait

    /**
     * Refreshes the database before each test.
     */
    use RefreshDatabase; // O segundo trait
}

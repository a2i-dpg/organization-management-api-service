<?php

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    const BASE_PATH = 'api/v1';

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}

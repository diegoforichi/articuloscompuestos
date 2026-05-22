<?php

namespace Tests;

use App\Models\ComponentType;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function metalComponentTypeId(): int
    {
        return (int) ComponentType::query()->where('code', 'metal')->firstOrFail()->id;
    }
}

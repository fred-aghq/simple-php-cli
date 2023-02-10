<?php

namespace App\DynamoDB\Fixture;

use Faker\Factory;
use Faker\Generator;

abstract class FixtureBase implements FixtureInterface
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function generate(int $iterations = 1): \Generator
    {
        for ($i = 0; $i <= $iterations; $i++) {
            yield $this->fakeRow();
        }
    }
}

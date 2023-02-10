<?php

namespace App\DynamoDB\Fixture;

interface FixtureInterface
{
    public function fakeRow(array $config = []): array;
}

<?php
namespace App\Interfaces;

interface RosterSerializer
{
    public function parseRoster( string $source ): array;
}

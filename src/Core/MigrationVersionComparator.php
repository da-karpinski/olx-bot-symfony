<?php

namespace App\Core;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;

class MigrationVersionComparator implements Comparator
{
    public function compare(Version $a, Version $b): int
    {
        return strcmp($this->versionWithoutNamespace($a), $this->versionWithoutNamespace($b));
    }

    private function versionWithoutNamespace(Version $version): string
    {
        $path = explode('\\', (string) $version);

        return array_pop($path);
    }
}
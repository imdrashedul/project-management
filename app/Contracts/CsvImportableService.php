<?php

namespace App\Contracts;
use Illuminate\Support\Collection;

interface CsvImportableService
{
    public function resolve(callable $import, Collection $records): mixed;
}

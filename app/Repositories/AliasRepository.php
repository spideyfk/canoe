<?php

namespace App\Repositories;

use App\Models\Alias;

class AliasRepository
{
    public function createAlias(array $data)
    {
        return Alias::create($data);
    }
}

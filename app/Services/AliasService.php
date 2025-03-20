<?php

namespace App\Services;

use App\Repositories\AliasRepository;

class AliasService
{
    protected AliasRepository $aliasRepository;

    public function __construct(AliasRepository $aliasRepository)
    {
        $this->aliasRepository = $aliasRepository;
    }

    public function createAlias(array $data)
    {
        return $this->aliasRepository->createAlias($data);
    }
}

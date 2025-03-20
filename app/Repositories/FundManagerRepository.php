<?php

namespace App\Repositories;

use App\Models\FundManager;

class FundManagerRepository
{
    public function createFundManager(array $data)
    {
        return FundManager::create($data);
    }
}

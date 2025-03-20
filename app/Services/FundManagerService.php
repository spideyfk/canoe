<?php

namespace App\Services;

use App\Repositories\FundManagerRepository;

class FundManagerService
{
    protected FundManagerRepository $fundManagerRepository;

    public function __construct(FundManagerRepository $fundManagerRepository)
    {
        $this->fundManagerRepository = $fundManagerRepository;
    }

    public function createFundManager(array $data)
    {
        return $this->fundManagerRepository->createFundManager($data);
    }
}

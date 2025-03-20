<?php

namespace App\Services;

use App\Events\DuplicateFundWarning;
use App\Repositories\FundRepository;

class FundService
{
    protected FundRepository $fundRepository;

    public function __construct(FundRepository $fundRepository)
    {
        $this->fundRepository = $fundRepository;
    }

    public function getFilteredFunds(?string $name, ?string $manager, ?int $year)
    {
        return $this->fundRepository->getFilteredFunds($name, $manager, $year);
    }

    public function updateFund(int $fundId, array $data)
    {
        return $this->fundRepository->updateFund($fundId, $data);
    }

    public function createFund($data) {
        // Check for duplicates
        $duplicateFund = $this->fundRepository->findPotentialDuplicates($data['name'], $data['manager_id']);
        if ($duplicateFund->isNotEmpty()) {
            event(new DuplicateFundWarning($duplicateFund));
            $response['warnings'] = [
                'message' => 'Potential duplicate funds detected.',
                'duplicates' => $duplicateFund,
            ];
            return $response;
        }

        // Create the fund
        return $this->fundRepository->create($data);
    }

    public function getPotentialDuplicates(string $name, int $managerId)
    {
        return $this->fundRepository->findPotentialDuplicates($name, $managerId);
    }
}

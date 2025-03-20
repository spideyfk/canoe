<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Fund;
use Illuminate\Support\Facades\DB;

class FundRepository
{
    //This will create both the fund and appropriate extra data like aliases.
    public function create(array $data): Fund
    {
        return DB::transaction(function () use ($data) {
            try {
                //Create the fund
                $fund = Fund::create([
                    'name' => $data['name'],
                    'start_year' => $data['start_year'],
                    'manager_id' => $data['manager_id'],
                ]);

                //Create aliases (if provided)
                if (isset($data['aliases'])) {
                    foreach ($data['aliases'] as $aliasData) {
                        $existingAlias = $fund->aliases()
                            ->where('name', $aliasData['name'])
                            ->first();

                        if (!$existingAlias) {
                            $fund->aliases()->create($aliasData);
                        }
                    }
                }

                //Sync companies (if provided). This also validates that the company ids provided exist.
                if (isset($data['company_ids'])) {
                    $this->validateCompanies($fund, $data['company_ids']);
                }

                return $fund->load('manager', 'aliases', 'companies');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }
    public function getFilteredFunds(?string $name, ?string $manager, ?int $year)
    {
        return Fund::with('manager')
            ->when($name, fn ($query) => $query->where('name', 'like', "%$name%"))
            ->when($manager, fn ($query) => $query->whereHas('manager', fn ($q) => $q->where('name', 'like', "%$manager%")))
            ->when($year, fn ($query) => $query->where('start_year', $year))
            ->get();
    }

    public function updateFund(int $fundId, array $data)
    {
        return DB::transaction(function () use ($fundId, $data) {
            try {
            $fund = Fund::findOrFail($fundId);

            $fund->update([
                'name' => $data['name'] ?? $fund->name,
                'start_year' => $data['start_year'] ?? $fund->start_year,
                'manager_id' => $data['manager_id'] ?? $fund->manager_id,
            ]);

            if (isset($data['aliases'])) {
                $fund->aliases()->delete();
                $fund->aliases()->createMany($data['aliases']);
            }

            if (isset($data['company_ids'])) {
                $this->validateCompanies($fund, $data['company_ids']);
            }

            return $fund->load('manager', 'aliases', 'companies');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }

    public function findPotentialDuplicates(string $name, int $managerId)
    {
        return Fund::with('aliases') // Eager load aliases
        ->where('manager_id', $managerId)
            ->where(function ($query) use ($name) {
                $query->where('name', $name)
                    ->orWhereHas('aliases', fn ($q) => $q->where('name', $name));
            })
            ->get();
    }

    private function validateCompanies(Fund $fund, array $companyIds)
    {
        $invalidCompanies = array_diff($companyIds, Company::pluck('id')->toArray());
        if (!empty($invalidCompanies)) {
            throw new \Exception('Invalid company IDs: ' . implode(', ', $invalidCompanies));
        }

        // Sync companies
        $fund->companies()->sync($companyIds);
    }
}

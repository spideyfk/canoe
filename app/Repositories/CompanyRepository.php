<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    public function createCompany(array $data)
    {
        return Company::create($data);
    }
}

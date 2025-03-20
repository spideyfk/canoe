<?php

namespace App\Http\Controllers;

use App\Services\FundService;
use Illuminate\Http\Request;

class FundController extends Controller
{
    protected FundService $fundService;

    public function __construct(FundService $fundService)
    {
        $this->fundService = $fundService;
    }

    public function index()
    {
        $name = request('name');
        $manager = request('manager');
        $year = request('year');

        $funds = $this->fundService->getFilteredFunds($name, $manager, $year);

        return response()->json($funds);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_year' => 'required|integer|min:2000|max:' . date('Y'),
            'manager_id' => 'required|exists:fund_managers,id',
            'aliases' => 'sometimes|array',
            'aliases.*.name' => 'required_with:aliases|string|max:255',
            'company_ids' => 'sometimes|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $fund = $this->fundService->createFund($data);

        return response()->json($fund, 201);
    }

    public function update(int $fundId, Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_year' => 'sometimes|integer|min:2000|max:' . date('Y'),
            'manager_id' => 'sometimes|exists:fund_managers,id',
            'aliases' => 'sometimes|array',
            'aliases.*.name' => 'required_with:aliases|string|max:255',
            'company_ids' => 'sometimes|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $fund = $this->fundService->updateFund($fundId, $data);

        return response()->json($fund);
    }

    public function getPotentialDuplicates(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:fund_managers,id',
        ]);

        $duplicates = $this->fundService->getPotentialDuplicates(
            $request->input('name'),
            $request->input('manager_id')
        );

        return response()->json($duplicates);
    }
}

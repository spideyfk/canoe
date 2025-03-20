<?php

namespace App\Http\Controllers;

use App\Services\FundManagerService;
use Illuminate\Http\Request;

class FundManagerController extends Controller
{
    protected FundManagerService $fundManagerService;

    public function __construct(FundManagerService $fundManagerService)
    {
        $this->fundManagerService = $fundManagerService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $fundManager = $this->fundManagerService->createFundManager($data);

        return response()->json($fundManager, 201);
    }
}

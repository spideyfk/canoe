<?php

namespace App\Http\Controllers;

use App\Services\AliasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AliasController extends Controller
{
    protected AliasService $aliasService;

    public function __construct(AliasService $aliasService)
    {
        $this->aliasService = $aliasService;
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'fund_id' => 'required|exists:funds,id',
        ]);

        $alias = $this->aliasService->createAlias($data);

        return response()->json($alias, 201);
    }
}

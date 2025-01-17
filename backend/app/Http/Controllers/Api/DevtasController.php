<?php

namespace App\Http\Controllers\Api;

use App\Models\Devta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DevtaResource;
use App\Http\Requests\StoreDevtaRequest;
use App\Http\Requests\UpdateDevtaRequest;
use App\Http\Controllers\Api\BaseController;


    /**
     * @group Devtas Management
     */
    
class DevtasController extends BaseController
{
    /**
     * All Devtas.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Devta::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('devta_name', 'like', '%' . $searchTerm . '%');
            });
        }
        $devtas = $query->Orderby('id', 'desc')->paginate(20);

        return $this->sendResponse(["Devtas"=>DevtaResource::collection($devtas),
        'pagination' => [
            'current_page' => $devtas->currentPage(),
            'last_page' => $devtas->lastPage(),
            'per_page' => $devtas->perPage(),
            'total' => $devtas->total(),
        ]], "Devtas retrieved successfully");
    }

    /**
     * Store Devta.
     */
    public function store(StoreDevtaRequest $request): JsonResponse
    {
        $devta = new Devta();
        $devta->devta_name = $request->input("devta_name");
        if(!$devta->save()) {
            dd($devta); exit;
        }
        return $this->sendResponse(['Devta'=> new DevtaResource($devta)], 'Devta Created Successfully');
    }

    /**
     * Show Devta.
     */
    public function show(string $id): JsonResponse
    {
        $devta = Devta::find($id);

        if(!$devta){
            return $this->sendError("Devta not found", ['error'=>'Devta not found']);
        }
        return $this->sendResponse(['Devta'=> new DevtaResource($devta)], "Devta retrieved successfully");
    }

    /**
     * Update Devta.
     */
    public function update(UpdateDevtaRequest $request, string $id): JsonResponse
    {
        $devta = Devta::find($id);
        if(!$devta){
            return $this->sendError("Devta not found", ['error'=>['Devta not found']]);
        }
        $devta->devta_name = $request->input('devta_name');
        $devta->save();
        return $this->sendResponse(["Devta"=> new DevtaResource($devta)], "Devta Updated successfully");
    }

    /**
     * Remove Devta.
     */
    public function destroy(string $id): JsonResponse
    {
        $devta = Devta::find($id);
        if(!$devta){
            return $this->sendError("Devta not found", ['error'=>'Devta not found']);
        }

        $devta->delete();

        return $this->sendResponse([], "devta deleted successfully");
    }

    /**
     * Fetch All Devta.
     */
    public function alldevtas(): JsonResponse
    {
        $devtas = Devta::all();

        return $this->sendResponse(["Devtas"=>DevtaResource::collection($devtas),
        ], "Devtas retrieved successfully");

    }
}
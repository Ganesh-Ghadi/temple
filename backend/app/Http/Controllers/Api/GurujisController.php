<?php

namespace App\Http\Controllers\Api;

use App\Models\Guruji;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\GurujiResource;
use App\Http\Requests\StoreGurujiRequest;
use App\Http\Requests\UpdateGurujiRequest;
use App\Http\Controllers\Api\BaseController;

    /**
     * @group Guruji Management
     */
class GurujisController extends BaseController
{
    /**
     * All Gurujies.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Guruji::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('guruji_name', 'like', '%' . $searchTerm . '%');
            });
        }
        $gurujis = $query->Orderby("id","desc")->paginate(20);

        return $this->sendResponse(["Gurujis"=>GurujiResource::collection($gurujis),
        'pagination' => [
            'current_page' => $gurujis->currentPage(),
            'last_page' => $gurujis->lastPage(),
            'per_page' => $gurujis->perPage(),
            'total' => $gurujis->total(),
        ]], "Gurujis retrieved successfully");
    }

    /**
     * Store Guruji.
     * @bodyParam guruji_name string The name of the guruji.
     */
    public function store(StoreGurujiRequest $request): JsonResponse
    {
        $guruji = new Guruji();
        $guruji->guruji_name = $request->input("guruji_name");
        if(!$guruji->save()) {
            dd($guruji); exit;
        }
        return $this->sendResponse(['Guruji'=> new GurujiResource($guruji)], 'Guruji Created Successfully');
    }

    /**
     * Show Guruji.
     */
    public function show(string $id): JsonResponse
    {
        $guruji = Guruji::find($id);

        if(!$guruji){
            return $this->sendError("Guruji not found", ['error'=>'Guruji not found']);
        }
        return $this->sendResponse(['Guruji'=> new GurujiResource($guruji)], "Guruji retrieved successfully");
    }

    /**
     * Update Guruji.
     * @bodyParam guruji_name string The name of the guruji.
     */
    public function update(UpdateGurujiRequest $request, string $id): JsonResponse
    {
        $guruji = Guruji::find($id);
        if(!$guruji){
            return $this->sendError("Guruji not found", ['error'=>['Guruji not found']]);
        }
        $guruji->guruji_name = $request->input("guruji_name");

        if(!$guruji->save()) {
            dd($guruji); exit;
        }
        return $this->sendResponse(["Guruji"=> new GurujiResource($guruji)], "Guruji Updated successfully");
    }

    /**
     * Delete Guruji.
     */
    public function destroy(string $id): JsonResponse
    {
        $guruji = Guruji::find($id);
        if(!$guruji){
            return $this->sendError("Guruji not found", ['error'=>'Guruji not found']);
        }
        $guruji->delete();
        return $this->sendResponse([], "Guruji deleted successfully");
    }

     /**
     * Fetch All Guruji.
     */
    public function allGurujis(): JsonResponse
    {
        $gurujis = Guruji::all();

        return $this->sendResponse(["Gurujis"=>GurujiResource::collection($gurujis),
        ], "Gurujis retrieved successfully");

    }
}
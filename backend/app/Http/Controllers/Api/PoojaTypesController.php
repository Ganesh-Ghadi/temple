<?php

namespace App\Http\Controllers\Api;

use App\Models\PoojaType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PoojaTypeResource;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StorePoojaTypeRequest;
use App\Http\Requests\UpdatePoojaTypeRequest;

    /**
     * @group Pooja Type Management
     */
    
class PoojaTypesController extends BaseController
{
     /**
     * All Pooja Types.
     */
    
    public function index(Request $request): JsonResponse
    {
        $query = PoojaType::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('pooja_type', 'like', '%' . $searchTerm . '%');
            });
        }
        $poojaTypes = $query->Orderby("id","desc")->paginate(5);

        return $this->sendResponse(["PoojaTypes"=>PoojaTypeResource::collection($poojaTypes),
        'pagination' => [
            'current_page' => $poojaTypes->currentPage(),
            'last_page' => $poojaTypes->lastPage(),
            'per_page' => $poojaTypes->perPage(),
            'total' => $poojaTypes->total(),
        ]], "Pooja Types retrieved successfully");
    }

     /**
     * Store Pooja Type.
     */
    public function store(StorePoojaTypeRequest $request): JsonResponse
    {
        $poojaType = new PoojaType();
        $poojaType->pooja_type = $request->input("pooja_type");
        $poojaType->devta_id = $request->input("devta_id");
        $poojaType->multiple = $request->input("multiple");
        $poojaType->contribution = $request->input("contribution");

        if(!$poojaType->save()) {
            dd($poojaType); exit;
        }
        return $this->sendResponse(['PoojaType'=> new PoojaTypeResource($poojaType)], 'Pooja Type Created Successfully');
    }

    /**
     * Show Pooja Type.
     */
    public function show(string $id): JsonResponse
    {
        $poojaType = PoojaType::find($id);

        if(!$poojaType){
            return $this->sendError("Pooja Type not found", ['error'=>'Pooja Type not found']);
        }
        return $this->sendResponse(['PoojaType'=> new PoojaTypeResource($poojaType)], "Pooja Type retrieved successfully");
    }

    /**
     * Update Pooja Type.
     */
    public function update(UpdatePoojaTypeRequest $request, string $id): JsonResponse
    {
        $poojaType = PoojaType::find($id);
        if(!$poojaType){
            return $this->sendError("Pooja Type not found", ['error'=>['Pooja Type not found']]);
        }
        $poojaType->pooja_type = $request->input("pooja_type");
        $poojaType->devta_id = $request->input("devta_id");
        $poojaType->multiple = $request->input("multiple");
        $poojaType->contribution = $request->input("contribution");
        $poojaType->save();
        return $this->sendResponse(["PoojaType"=> new PoojaTypeResource($poojaType)], "Pooja Type Updated successfully");
    }

    /**
     * Remove Pooja Type.
     */
    public function destroy(string $id): JsonResponse
    {
        $poojaType = PoojaType::find($id);
        if(!$devtapoojaType){
            return $this->sendError("Pooja Type not found", ['error'=>'Pooja Type not found']);
        }
        $poojaType->delete();
        return $this->sendResponse([], "Pooja Type deleted successfully");
    }

    
}
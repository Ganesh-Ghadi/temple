<?php

namespace App\Http\Controllers\Api;

use App\Models\PoojaDate;
use App\Models\PoojaType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PoojaDateResource;
use App\Http\Resources\PoojaTypeResource;
use App\Http\Controllers\Api\BaseController;

    /**
     * @group Pooja Date Management
     */
    
class PoojaDatesController extends BaseController
{
    /**
     * All Pooja Dates.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PoojaDate::with("poojaType");

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('pooja_date', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('poojaType', function ($query) use ($searchTerm) {
                    $query->where('pooja_type', 'like', '%' . $searchTerm . '%');
                });
            });

            
        }
        $poojaDates = $query->Orderby("id","desc")->paginate(20);

        return $this->sendResponse(["PoojaDates"=>PoojaDateResource::collection($poojaDates),
        'pagination' => [
            'current_page' => $poojaDates->currentPage(),
            'last_page' => $poojaDates->lastPage(),
            'per_page' => $poojaDates->perPage(),
            'total' => $poojaDates->total(),
        ]], "Pooja Dates retrieved successfully");
        
    }

    /**
     * Store Pooja Date.
     */
    // public function store(Request $request)
    // {
    //     $poojaDate = new PoojaDate();
    //     $poojaDate->pooja_type_id = $request->input("pooja_type_id");
    //     $poojaDate->pooja_date = $request->input("pooja_date");
        
    //     if(!$poojaDate->save()) {
    //         return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
    //     }
    //     return $this->sendResponse(['PoojaDate'=> new PoojaDateResource($poojaDate)], 'Pooja Dates Created Successfully');
    // }
    public function store(Request $request)
    {
        // Validate the incoming data to ensure pooja_type_id is present and pooja_dates is an array
        $request->validate([
            'pooja_type_id' => 'required|integer|exists:pooja_types,id', // Adjust validation based on your schema
            'pooja_dates' => 'required|array|min:1',  // Validate pooja_dates as an array and at least one date
            'pooja_dates.*' => 'required|date', // Ensure each item in the pooja_dates array is a valid date
        ]);
    
        // Loop through the pooja_dates array and save each date as a new record
        foreach ($request->input('pooja_dates') as $date) {
            $poojaDate = new PoojaDate();
            $poojaDate->pooja_type_id = $request->input('pooja_type_id');
            $poojaDate->pooja_date = $date;
    
            // Save the record and handle any errors
            if (!$poojaDate->save()) {
                return $this->sendError("Error while saving data", ['error' => ['Error while saving data']]);
            }
        }
    
        // Return success response after all dates are saved
        return $this->sendResponse([], 'Pooja Dates Created Successfully');
    }
    
    /**
     * Show Pooja Date.
     */
    public function show(string $id)
    {
        $poojaDate = PoojaDate::find($id);

        if(!$poojaDate){
            return $this->sendError("Pooja Date not found", ['error'=>'Pooja Date not found']);
        }
        return $this->sendResponse(['PoojaDate'=> new PoojaDateResource($poojaDate)], "Pooja Date retrieved successfully");
    }

    /**
     * Update Pooja Date.
     */
    public function update(Request $request, string $id)
    {
        $poojaDate = PoojaDate::find($id);
        if(!$poojaDate){
            return $this->sendError("Pooja Date not found", ['error'=>['Pooja Date not found']]);
        }
        $poojaDate->pooja_type_id = $request->input("pooja_type_id");
        $poojaDate->pooja_date = $request->input("pooja_date");
              
        if(!$poojaDate->save()) {
            return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(['PoojaDate'=> new PoojaDateResource($poojaDate)], 'PoojaDate Updated Successfully');
    }

    /**
     * Delete Pooja Date.
     */
    public function destroy(string $id)
    {
        $poojaDate = PoojaDate::find($id);
        if(!$poojaDate){
            return $this->sendError("Pooja Date not found", ['error'=>'Pooja Date not found']);
        }
        $poojaDate->delete();
        return $this->sendResponse([], "Pooja Date deleted successfully");
    }



    /**
     * Show Pooja Dates.
     */
    public function showPoojaDates(string $id)
    {
        $poojaDates = PoojaDate::where('pooja_type_id', $id)->get();

        return $this->sendResponse(['PoojaDates'=>PoojaDateResource::collection($poojaDates)], "Pooja Dates retrieved successfully");
    }

   
    
}
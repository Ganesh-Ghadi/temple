<?php

namespace App\Http\Controllers\Api;

use App\Models\Devta;
use Illuminate\Http\Request;
use App\Models\AnteshteeAmount;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DevtaResource;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\AnteshteeAmountResource;
use App\Http\Requests\UpdateAnteshteeAmountRequest;

class AnteshteeAmountsController extends BaseController
{
    /**
     * All Anteshtee Date Amounts.
     */
    public function index(Request $request): JsonResponse
    {
        // Apply pagination directly on the query
        $anteshteeDate = AnteshteeAmount::paginate(20);
        return $this->sendResponse(
            [
                "AnteshteeDates" => AnteshteeAmountResource::collection($anteshteeDate),
                'pagination' => [
                    'current_page' => $anteshteeDate->currentPage(),
                    'last_page' => $anteshteeDate->lastPage(),
                    'per_page' => $anteshteeDate->perPage(),
                    'total' => $anteshteeDate->total(),
                ]
            ],
            "Anteshtee Date Amounts retrieved successfully"
        );
    }

    /**
     * Show Anteshtee Amounts.
     */
    public function show(string $id): JsonResponse
    {
        $anteshteeDate = AnteshteeAmount::find($id);

        if(!$anteshteeDate){
            return $this->sendError("Anteshtee amount not found", ['error'=>'Anteshtee amount not found']);
        }
        return $this->sendResponse(['AnteshteeDate'=> new AnteshteeAmountResource($anteshteeDate)], "Anteshtee amount retrieved successfully");
    }

    /**
     * Update Anteshtee Amounts.
     */
    public function update(UpdateAnteshteeAmountRequest $request, string $id): JsonResponse
    {
        $anteshteeAmount = AnteshteeAmount::find($id);
        if(!$anteshteeAmount){
            return $this->sendError("Anteshtee amount not found", ['error'=>['Anteshtee amount not found']]);
        }
        $anteshteeAmount->day_9_amount = $request->input('day_9_amount');
        $anteshteeAmount->day_10_amount = $request->input('day_10_amount');
        $anteshteeAmount->day_11_amount = $request->input('day_11_amount');
        $anteshteeAmount->day_12_amount = $request->input('day_12_amount');
        $anteshteeAmount->day_13_amount = $request->input('day_13_amount');
        $anteshteeAmount->save();
        return $this->sendResponse(["AnteshteeDate"=> new AnteshteeAmountResource($anteshteeAmount)], "Anteshtee Amount Updated successfully");
    }
    
}
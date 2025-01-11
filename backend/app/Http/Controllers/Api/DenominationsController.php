<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Barryvdh\DomPDF\PDF;
use App\Models\Denomination;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DenominationResource;
use App\Http\Requests\StoreDenominationRequest;
use App\Http\Requests\UpdateDenominationRequest;

    /**
     * @group Denomination Management
     */
    
class DenominationsController extends BaseController
{
    /**
     * All Denominations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Denomination::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('amount', 'like', '%' . $searchTerm . '%')
                ->orWhere('deposit_date', 'like', '%'. $searchTerm . '%');
            });
        }
        $denominations = $query->orderBy("id", "DESC")->paginate(15);

        return $this->sendResponse(["Denominations"=>DenominationResource::collection($denominations),
        'Pagination' => [
            'current_page' => $denominations->currentPage(),
            'last_page' => $denominations->lastPage(),
            'per_page' => $denominations->perPage(),
            'total' => $denominations->total(),
        ]], "Denominations retrieved successfully");
    }

    /**
     * Store Denomination.
     * @bodyParam deposit_date The date of the deposit.
     * @bodyParam n_2000 integer The name of the note 2000.
     * @bodyParam n_500 integer The name of the note 500.
     * @bodyParam n_200 integer The name of the note 200.
     * @bodyParam n_100 integer The name of the note 100.
     * @bodyParam n_50 integer The name of the note 50.
     * @bodyParam n_20 integer The name of the note 20.
     * @bodyParam n_10 integer The name of the note 10.
     * @bodyParam c_20 integer The name of the coin 20.
     * @bodyParam c_10 integer The name of the coin 10.
     * @bodyParam c_5 integer The name of the coin 5.
     * @bodyParam c_2 integer The name of the coin 2.
     * @bodyParam c_1 integer The name of the coin 1.
     * @bodyParam amount decimal The name of the amount.
     */
    public function store(StoreDenominationRequest $request): JsonResponse
    {
        $denomination = new Denomination();
        $denomination->deposit_date = $request->input("deposit_date");
        $denomination->n_2000 = $request->input("n_2000");
        $denomination->n_500 = $request->input("n_500");
        $denomination->n_200 = $request->input("n_200");
        $denomination->n_100 = $request->input("n_100");
        $denomination->n_50 = $request->input("n_50");
        $denomination->n_20 = $request->input("n_20");
        $denomination->n_10 = $request->input("n_10");

        $denomination->c_20 = $request->input("c_20");
        $denomination->c_10 = $request->input("c_10");
        $denomination->c_5 = $request->input("c_5");
        $denomination->c_2 = $request->input("c_2");
        $denomination->c_1 = $request->input("c_1");
        $denomination->amount = $request->input("amount");

        if(!$denomination->save()) {
          return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(['Denomination'=> new DenominationResource($denomination)], 'Denomination Created Successfully');
    }

    /**
     * Show Denomination.
     */
    public function show(string $id): JsonResponse
    {
        $denomination = Denomination::find($id);

        if(!$denomination){
            return $this->sendError("Denomination not found", ['error'=>['Denomination not found']]);
        }
        
        return $this->sendResponse(["Denomination"=> new DenominationResource($denomination)], "denomination retrieved successfully");
    }

    /**
     * Update Denomination.
     * @bodyParam deposit_date The date of the deposit.
     * @bodyParam n_2000 integer The name of the note 2000.
     * @bodyParam n_500 integer The name of the note 500.
     * @bodyParam n_200 integer The name of the note 200.
     * @bodyParam n_100 integer The name of the note 100.
     * @bodyParam n_50 integer The name of the note 50.
     * @bodyParam n_20 integer The name of the note 20.
     * @bodyParam n_10 integer The name of the note 10.
     * @bodyParam c_20 integer The name of the coin 20.
     * @bodyParam c_10 integer The name of the coin 10.
     * @bodyParam c_5 integer The name of the coin 5.
     * @bodyParam c_2 integer The name of the coin 2.
     * @bodyParam c_1 integer The name of the coin 1.
     * @bodyParam amount decimal The name of the amount.
     */
    
    public function update(UpdateDenominationRequest $request, string $id): JsonResponse
    {
        $denomination = Denomination::find($id);
        if(!$denomination){
            return $this->sendError("Denomination not found", ['error'=>['Denomination not found']]);
        }
        $denomination->deposit_date = $request->input("deposit_date");
        $denomination->n_2000 = $request->input("n_2000");
        $denomination->n_500 = $request->input("n_500");
        $denomination->n_200 = $request->input("n_200");
        $denomination->n_100 = $request->input("n_100");
        $denomination->n_50 = $request->input("n_50");
        $denomination->n_20 = $request->input("n_20");
        $denomination->n_10 = $request->input("n_10");

        $denomination->c_20 = $request->input("c_20");
        $denomination->c_10 = $request->input("c_10");
        $denomination->c_5 = $request->input("c_5");
        $denomination->c_2 = $request->input("c_2");
        $denomination->c_1 = $request->input("c_1");
        $denomination->amount = $request->input("amount");

        if(!$denomination->save()) {
            return $this->sendError("Error while updating data", ['error'=>['Error while updating data']]);
          }
        return $this->sendResponse(["Denomination"=> new DenominationResource($denomination)], "Denomination Updated successfully");
    }

    /**
     * Delete Denomination
     */
    public function destroy(string $id): JsonResponse
    {
        $denomination = Denomination::find($id);
        if(!$denomination){
            return $this->sendError("Denomination not found", ['error'=>'Denomination not found']);
        }

        $denomination->delete();

        return $this->sendResponse([], "Denomination deleted successfully");
    }

     /**
     * Generate Denomination
     */

    public function generateDenomination(string $id)
    {
        $denomination = Denomination::find($id);
        if(!$denomination){
            return $this->sendError("Denomination not found", ['error'=>['Denomination not found']]);
        }
        
        if(!empty($denomination->denomination_file) && Storage::exists('public/Denomination/'.$denomination->denomination_file)) {
            Storage::delete('public/Denomination/'.$denomination->denomination_file);
        }

        $data = [
            'denomination' => $denomination,
        ];

        // Render the Blade view to HTML
        $html = view('Denomination.denomination', $data)->render();

        // Create a new mPDF instance
        // $mpdf = new Mpdf();
            // $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'L']);  // 'P' is for portrait (default)
            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
        
            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
        
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L',
                'fontDir' => array_merge($fontDirs, [
                    storage_path('fonts/'), // Update to point to the storage/fonts directory
                ]),
                'fontdata' => $fontData + [
                    'notosansdevanagari' => [
                        'R' => 'NotoSansDevanagari-Regular.ttf',
                        'B' => 'NotoSansDevanagari-Bold.ttf',
                    ],
                ],
                'default_font' => 'notosansdevanagari',
            ]);

        // Write HTML to the PDF
        $mpdf->WriteHTML($html);
        $randomNumber = rand(1000, 9999);
        // Define the file path for saving the PDF
        $filePath = 'public/Denomination/denomination' . time(). $randomNumber . '.pdf'; // Store in 'storage/app/invoices'
        $fileName = basename($filePath); // Extracts 'invoice_{timestamp}{user_id}.pdf'
        $denomination->denomination_file = $fileName;
        $denomination->save();
      
        // Save PDF to storage
        Storage::put($filePath, $mpdf->Output('', 'S')); // Output as string and save to storage

        // Output the PDF for download
        return $mpdf->Output('denomination.pdf', 'D'); // Download the PDF
        // return $this->sendResponse([], "Invoice generated successfully");
    }

}
<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Logic\ApiExternalLogic;
use Illuminate\Http\Request;


class ApiExternalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function uploadDocumentPmc(Request $request)
    {
        $response = new ApiResponse();
        try {
            $parameter = (array)$request->input();
            $item_id = $parameter['item_id'];
            $type_of_service = $parameter['type_of_service'];
            $number_claim = $parameter['number_claim'];

            $response_api_higgs = ApiExternalLogic::ReadApiHiggsHub($item_id);
            $response_upload_document = '';

            if (isset($response_api_higgs['error']) && !empty($item_id)) {
                return ApiResponse::emptydata("Job not found", 500, $response_api_higgs);
            } else if (!empty($item_id) &&  !empty($type_of_service) && !empty($number_claim)) {

                $claimsid = ApiExternalLogic::ClaimRecordsLists($number_claim);

                $response_upload_document = ApiExternalLogic::uploadDocumentPmc($item_id, $claimsid, $response_api_higgs);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Error' . $e, 404, $response);
        }
        return ApiResponse::success("Success", 200, $response_upload_document, "");
    }
}

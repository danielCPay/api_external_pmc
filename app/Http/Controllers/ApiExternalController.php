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

            $claimsid = 0;

            $response_api_higgs = ApiExternalLogic::ReadApiHiggsHub($item_id);
            $response_upload_document = '';

            if (isset($response_api_higgs['error']) && !empty($item_id) && !empty($number_claim)) {
                return ApiResponse::emptydata("Job not found", 500, $response_api_higgs);
            } else if (!empty($item_id) &&  !empty($type_of_service) && !empty($number_claim)) {

                $claimsid = ApiExternalLogic::ClaimRecordsLists($number_claim);

                if ($claimsid > 0) {
                    $response_upload_document = ApiExternalLogic::uploadDocumentPmc($item_id, $claimsid, $response_api_higgs);

                    if (is_int($response_upload_document[0])) {
                        $code = $response_upload_document[0];
                        $message = $response_upload_document[1];
                        return ApiResponse::error($message, $code, $response);
                    }
                } else {
                    return ApiResponse::error('Error # Claim does not exist', 404, $response);
                }
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Error' . $e, 404, $response);
        }
        return ApiResponse::success("Success", 200, $response_upload_document, false);
    }
}

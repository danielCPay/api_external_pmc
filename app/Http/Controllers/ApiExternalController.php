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

            $response_api_higgs = ApiExternalLogic::ReadApiHiggsHub($item_id);

            if (isset($response['error']) && !empty($item_id)) {
                return ApiResponse::emptydata("Job not found", 500, $response);
            } else if (!empty($item_id) &&  !empty($type_of_service)) {

                $response_upload_document = ApiExternalLogic::uploadDocumentPmc($response_api_higgs);
                var_dump($response_upload_document);
                $response = ApiExternalLogic::updateRecordPmc($item_id);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Error' . $e, 404, $response);
        }
        return ApiResponse::success("Success", 200, $response, "");
    }
}

<?php

namespace App\Logic;

# use App\Models\BoardsModel;
use App\Class\General;
use App\Http\Responses\ApiResponse;
use HTTP_Request2;
use HTTP_Request2_Exception;

class ApiExternalLogic
{
    public static function GenerarTokenSeguridad()
    {
        $username = env('CURLOPT_USER');
        $password = env('CURLOPT_PWD');
        $xapikey = env('X_API_KEY');
        $urlbase = env('URL_BASE');
        $username_post_fieds = env('CURLOPT_POSTFIELDS_NAME');
        $password_post_fieds = env('CURLOPT_POSTFIELDS_PASSWORD');

        $token = '';

        $curl = curl_init();

        $options = array(
            CURLOPT_URL => $urlbase . "webservice/Users/Login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 80,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "X-ENCRYPTED: 0",
                "x-api-key:" . $xapikey
            ],
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => "userName=" . $username_post_fieds . "&password=" . $password_post_fieds

        );

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if (!General::isEmpty($result)) {
            foreach ($result['result'] as $k => $v) {
                if ($k == 'token') {
                    $token = $v;
                }
            }

            if ($err) {
                $response = "CURL Error #:" . $err;
            }
        }

        return $token;
    }
    public static function uploadDocumentPmc($item_id, $claimsid, array $data)
    {
        $userName = env('CURLOPT_USER');
        $password = env('CURLOPT_PWD');
        $x_api_key = env('X_API_KEY');
        $x_token = ApiExternalLogic::GenerarTokenSeguridad();
        $urlbase = env('URL_BASE');
        $result = '';
        $message = '';
        $success = 0;

        if (!empty($data['documents']['final_invoice'])) {

            foreach ($data['documents']['final_invoice'] as $items) {
                $final_invoice_documents[] = $items;
            }

            $links_dropbox = ApiExternalLogic::AssignFilesDropbox($item_id, $final_invoice_documents);
            $dbLink = '';

            foreach ($links_dropbox as $item_url) {

                $dbLink = $item_url;

                try {

                    $dbLink = preg_replace('/\bdl=[^&]*&?/', '', $dbLink);
                    $dbLink .= (strpos($dbLink, '?') === false ? '?' : '&') . 'dl=1';

                    $explode_dbLink = explode("?", $dbLink);

                    $explode_file = $explode_dbLink[0];
                    $explode_file = explode(".", $explode_file);

                    $explode_name = $explode_file[2];
                    $explode_name = explode("/", $explode_name);

                    $file_extension = $explode_file[3];
                    $file_name = $explode_name[4];

                    $mime_type  = General::mimetypes();
                    $file_mime_type = array_search($file_extension, $mime_type);

                    $url =   $urlbase . 'webservice/Documents/Record';
                    $headers = [
                        'x-api-key:' . $x_api_key,
                        'x-token: ' . $x_token,
                        'Content-Type: multipart/form-data'
                    ];

                    $cf = new \CURLFile($dbLink, $file_mime_type, $file_name . '.' . $file_extension);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    curl_setopt($ch, CURLOPT_USERPWD, "{$userName}:{$password}");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, [
                        'notes_title' => $file_name,
                        'filelocationtype' => 'I',
                        'filename' => $cf,
                        'document_type' => '25670',
                        'folderid' => 'T1'
                    ]);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                    $response = curl_exec($ch);
                    $err = curl_error($ch);

                    if ($err) {
                        $response = "CURL Error #:" . $err;
                    }

                    $result = json_decode($response, true);
                    $status = 0;
                    $notesid = 0;

                    if (isset($result['status'])) {
                        $status = $result['status'];
                        if ($status === 1) {
                            $notesid = $result['result']['id'];
                            $response = ApiExternalLogic::updateRecordPmc($notesid, $claimsid);
                        }
                    }
                } catch (\Exception $e) {
                    var_dump($e);
                }
            }
        }
        return [$result, $message, $success];
    }
    public static function readApiHiggsHub($item_id)
    {
        $urlbase = env('URL_BASE_HIGGS_HUB');
        $xapikey = env('X_API_KEY_HIGGS_HUB');

        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $urlbase . "claimPay?item_id=" . $item_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-api-key:" . $xapikey
            ]
        );

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt_array($curl, $options);

        $response_api = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $response_api = "CURL Error #:" . $err;
        }

        $result = json_decode($response_api, true);

        return $result;
    }
    public static function updateRecordPmc($notesid, $claimsid)
    {

        $x_api_key = env('X_API_KEY');
        $x_token = ApiExternalLogic::GenerarTokenSeguridad();
        $urlbase = env('URL_BASE');
        $username = env('CURLOPT_USER');
        $password = env('CURLOPT_PWD');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $urlbase . 'webservice/Documents/Record/' . $notesid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $x_api_key,
            'x-token:' . $x_token,
            'Content-Type: application/json'
        ],);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"claim\":\"$claimsid\"\n}");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        $result = json_decode($response, true);

        $err = curl_error($ch);
        curl_close($ch);
    }
    public static function AssignFilesDropbox($item_id, array $data)
    {
        $files_dropbox = [];

        foreach ($data as $item) {
            $explode_file = explode(".", $item['name']);
            $file_extension = count($explode_file) === 2 ? $explode_file[1] : $explode_file[count($explode_file) - 1];
            $date_and_time = date("Y-m-d-H:i:s");
            $string_without_space = str_replace(' ', '', $explode_file[0]);

            $new_file_dropbox = ApiExternalLogic::ReadApiDropbox($string_without_space . '_' . $date_and_time, $item_id, $item['file'], $file_extension);

            if (isset($new_file_dropbox['url'])) {
                $files_dropbox[] = $new_file_dropbox['url'];
            } else if (isset($new_file_dropbox['error'])) {
                $files_dropbox[] = $new_file_dropbox['error'];
            } else {
                $files_dropbox[] = $new_file_dropbox;
            }
        }

        return $files_dropbox;
    }
    public static function ReadApiDropbox($name_file, $name_folder, $public_url, $file_extension)
    {
        $urlbase = env('URL_BASE_DROPBOX');
        $url_dropbox = '';
        $request = new HTTP_Request2($urlbase . "dropbox/" . $name_file .  "." . $file_extension . "/" . $name_folder . "/" . $public_url, HTTP_Request2::METHOD_GET);
        try {
            $response = $request->send();
            if (200 == $response->getStatus()) {
                $url_dropbox =  json_decode($response->getBody(), true);
            } else {
                echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                    $response->getReasonPhrase();
            }
        } catch (HTTP_Request2_Exception $e) {
            $url_dropbox = 'Error: ' . $e->getMessage();
            echo 'Error: ' . $e->getMessage();
        }
        return $url_dropbox;
    }
    public static function ClaimRecordsLists($number_claim)
    {
        $username = env('CURLOPT_USER');
        $password = env('CURLOPT_PWD');

        $xapikey = env('X_API_KEY');
        $urlbase = env('URL_BASE');

        $xtoken = ApiExternalLogic::GenerarTokenSeguridad();
        $curl = curl_init();

        $claimsid = 0;

        if ($xtoken != "") {

            $options = array(
                CURLOPT_URL => $urlbase . "webservice/Claims/RecordsList",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 100,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "x-api-key:" . $xapikey,
                    "x-token:" . $xtoken,
                    "x-condition:" . '{"fieldName":"claim_number","operator":"e","value":"' . $number_claim . '"}'
                ],
                CURLOPT_USERPWD => "$username:$password"
            );

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt_array($curl, $options);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $result = json_decode($response, true);

            if (!General::isEmpty($result)) {

                foreach ($result['result']['records'] as $k => $v) {
                    $claimsid = $k;
                }

                if ($err) {
                    $response = "CURL Error #:" . $err;
                }
            }
        } else {
            return [[], "Error al Generar Token PMC", "error"];
        }
        return $claimsid;
    }
}

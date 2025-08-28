<?php

namespace app\Traits;

trait handleResponseTrait
{

    public function returnErrorMessage($errorMessage, $errorCode)
    {
        return response()->json([
            'status' => false,
            'error_message' => $errorMessage,
            'error_code' => $errorCode,

        ]);
    }


    public function returnData($key, $value, $msg, $successCode)
    {
        return response()->json([
            'status' => true,
            'success_code' => $successCode,
            $key => $value, //data = key and value.... ex: category=>[id,name,description]
            'success_message' => $msg,

        ]);
    }
    public function returnSuccessMessage($successCode, $msg)
    {
        return response()->json([
            'status' => true,
            'success_code' => $successCode,
            'success_message' => $msg,

        ]);
    }
}

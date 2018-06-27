<?php
/**
 * Error code definition.
 *
 * System/framework/exception: the errno is less than 1000.
 *
 * | Module                | Specific Error Code  |
 * | 1000 (auto increment) | 000 (auto increment) |
 */
class ErrCode {

    const OK = 0;

    const ERR_SYSTEM     = 1;
    const ERR_INVALID_PARAMS = 2;
    const ERR_LOGIN_FAILED = 3;
    const ERR_NOT_LOGIN = 4;
    const ERR_DUPLICATE_ACCOUNT = 5;
    const ERR_UPLOAD = 6;

    /**
     * @param array  $arrResponse
     * @param int    $intErrCode
     * @param string $strErrMsg   Use the default error message if the parameter is not provided.
     *
     * @return array
     */
    public static function format($arrResponse, $intErrCode, $strErrMsg='ok') {//{{{//
        return [
            'code' => $intErrCode,
            'msg'  => $strErrMsg,
            'data' => $arrResponse,
        ];
    }//}}}//

}

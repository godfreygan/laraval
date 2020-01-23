<?php
/**
 * general response's status code,
 * status code is nearly the same as those suggested for XML-RPC at the
 * following specs: http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Constant;

class Status
{
    const SUCCESS           = 0;
    const PARSE_ERROR       = -32700;
    const INVALID_REQUEST   = -32600;
    const METHOD_NOT_EXISTS = -32601;
    const INVALID_PARAMS    = -32602;
    const INTERNAL_ERROR    = -32603;
    const TRANSPORT_ERROR   = -32300;

    // parse error
    const UNSUPPORTED_ENCODING = -32701;
    const INVALID_CHARACTER_FOR_ENCODING = -32702;

    const SUCCESS_TRANSLATED           = 'success';
    const PARSE_ERROR_TRANSLATED       = 'invalid request was received by the server';
    const INVALID_REQUEST_TRANSLATED   = 'The sent is not a valid request object';
    const METHOD_NOT_EXISTS_TRANSLATED = 'The method does not exist / is not available';
    const INVALID_PARAMS_TRANSLATED    = 'invalid method parameter(s)';
    const INTERNAL_ERROR_TRANSLATED    = 'internal error';
    const TRANSPORT_ERROR_TRANSLATED   = 'transport error';

    // parse error
    const UNSUPPORTED_ENCODING_TRANSLATED = 'parse error. unsupported encoding';
    const INVALID_CHARACTER_FOR_ENCODING_TRANSLATED = 'parse error. invalid character for encoding';

    public static function translate($code)
    {
        $ret = 'unknown error';

        switch ($code) {
            case self::SUCCESS:
                $ret = 'success';
                break;
            case self::PARSE_ERROR:
                $ret = 'invalid request was received by the server';
                break;
            case self::INVALID_REQUEST:
                $ret = 'The sent is not a valid request object';
                break;
            case self::METHOD_NOT_EXISTS:
                $ret = 'The method does not exist / is not available';
                break;
            case self::INVALID_PARAMS:
                $ret = 'invalid method parameter(s)';
                break;
            case self::INTERNAL_ERROR:
                $ret = 'internal error';
                break;
            case self::TRANSPORT_ERROR:
                $ret = 'transport error';
                break;
            case self::UNSUPPORTED_ENCODING:
                $ret = 'parse error. unsupported encoding';
                break;
            case self::INVALID_CHARACTER_FOR_ENCODING:
                $ret = 'parse error. invalid character for encoding';
                break;
        }

        return $ret;
    }
}

<?php
/**
 * response of LightService
 *
 * @author yuanbaoju
 */

namespace LightService\Jsonrpc\Message\Response;

use LightService\ErrorResult;

class Response
{
    public static function success($result, $id = null)
    {
        $rep = new Success();
        $rep->result = $result;

        if (func_num_args() > 1) {
            $rep->id = $id;
        }

        return $rep;
    }

    public static function error($error, $id = null)
    {
        $rep = new Error();

        if (is_array($error)) {
            $rep->error->code = $error['code'];

            if (array_key_exists('message', $error)) {
                $rep->error->message = $error['message'];
            }

            if (array_key_exists('data', $error)) {
                $rep->error->data = $error['data'];
            }
        } elseif ($error instanceof ErrorResult) {
            $rep->error->code = $error->code;
            $rep->error->message = $error->message;
        } else {
            $rep->error->code = $error;
        }

        if (func_num_args() > 1) {
            $rep->id = $id;
        }

        return $rep;
    }
}

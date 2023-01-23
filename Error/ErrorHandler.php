<?php

namespace App;
use App\Response;

class ErrorHandler {

    public static function handle($errno, $errstr, $errfile, $errline): void {
        Response::json(['message' => 'some error'], 500);
    }

}

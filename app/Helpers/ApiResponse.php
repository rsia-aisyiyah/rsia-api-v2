<?php

namespace App\Helpers;

class ApiResponse
{
  public static function success($message = null, $data = [])
  {
    $body = [
      'message' => $message,
    ];

    if (!empty($data)) {
      $body['data'] = $data;
    }

    return response()->json($body, 200);
  }

  public static function successWithData($data, $message = null)
  {
    return response()->json([
      'message' => $message,
      'data'    => $data,
    ], 200);
  }

  public static function error($message, $error, $trace = null, $status = 400)
  {
    $resp = [
      'message' => $message,
      'error'   => $error,
    ];

    if ($trace) {
      $resp['trace'] = $trace;
    }

    return response()->json($resp, $status);
  }

  public static function errorWithData($data, $message, $error, $status = 400)
  {
    return response()->json([
      'message' => $message,
      'error'   => $error,
      'data'    => $data,
    ], $status);
  }

  public static function notFound($message = "")
  {
    return response()->json([
      'message' => $message,
      'error'   => "Cannot find data or resource",
    ], 404);
  }

  public static function unauthorized($message = '')
  {
    return response()->json([
      'message' => $message,
      'error'   => 'Unauthorized',
    ], 401);
  }

  public static function forbidden($message = '')
  {
    return response()->json([
      'message' => $message,
      'error'   => 'Forbidden',
    ], 403);
  }

  public static function validationError($message = '')
  {
    return response()->json([
      'message' => $message,
      'error'   => 'Validation Error',
    ], 422);
  }

  public static function internalServerError($message = '')
  {
    return response()->json([
      'message' => $message,
      'error'   => 'Internal Server Error',
    ], 500);
  }

  public static function badGateway($message = '')
  {
    return response()->json([
      'message' => $message,
      'error'   => 'Bad Gateway',
    ], 502);
  }

  public static function withToken($isSuccess, $token, $others = [])
  {
    $data = [];

    // Check if $others is not an array
    if (!is_array($others)) {
      throw new \InvalidArgumentException('Others must be an array');
    }

    // Loop through $others and add to $data
    foreach ($others as $key => $value) {
      $data[$key] = $value;
    }

    $data['access_token'] = $token;

    return response()->json($data, 200);
  }
}

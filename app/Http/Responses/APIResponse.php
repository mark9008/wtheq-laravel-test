<?php

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Throwable;

class APIResponse
{
    public static function LoginResponse($token, User $user, array $headers = []): JsonResponse
    {
        return Response()->json([
            'data' => UserResource::make($user),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,

        ], 200)->withHeaders($headers);
    }

    public static function LogoutResponse(array $headers = []): JsonResponse
    {
        return Response()->json([
            'message' => 'Logged out successfully',
            'status' => 200,
        ], 200)->withHeaders($headers);
    }

    public static function ErrorsResponse(string $title, string $detail, throwable $exception = null, int $status = 422, array $headers = [], $logError = false): JsonResponse
    {
        if ($logError)
            if ($exception?->getCode() === 0 && !$exception instanceof ModelNotFoundException)
                \Log::error(
                    "status code: {$exception->getCode()}\n\n" .
                    "file: {$exception->getFile()}\n\n" .
                    "line: {$exception->getLine()}\n\n" .
                    "message: {$exception->getMessage()}\n\n" .
                    $exception->getTraceAsString()
                );

        $errorArr = array();
        if (str_contains($detail, 'No query results for model')) {
            $detail = "Provided Data was not found";
            $status = 404;
        }

        if (str_contains($detail, 'SQLSTATE')) {
            $errorArr['DBError'] = $detail;
            $detail = 'Oops! Please refresh your page. If the issue persists, email us at support@complade.com. Thanks for your patience!"';
        }


        $errorArr = array_merge($errorArr, [
            "title" => $title,
            "details" => ucfirst($detail),
            'status' => $status,
        ]);

        return Response()->json([
            'errors' => $errorArr
        ], $status)->withHeaders($headers);
    }

    public static function CreatedSuccessfully($data, string $message = 'created successfully', array $additional = [], array $headers = []): JsonResponse
    {
        $responseBody = [
            'data' => $data,
            'message' => $message,
            'status' => 201,
        ];

        if (!empty($additional))
            $responseBody['additional'] = $additional;


        return Response()->json($responseBody, 201)->withHeaders($headers);
    }

    public static function DataResponse($data, $additional = [], array $headers = array()): JsonResponse
    {
        $responseBody = [
            'data' => $data,
            'status' => 200,
        ];

        if (!empty($additional))
            $responseBody['additional'] = $additional;

        return Response()->json($responseBody, 200)->withHeaders($headers);
    }
    public static function UpdatedSuccessfully($model, string $message = 'updated successfully', array $headers = []): JsonResponse
    {
        return Response()->json([
            'data' => $model,
            'message' => ucfirst($message),
            'status' => 200,
        ], 200)->withHeaders($headers);
    }

    /**
     * @param $message
     * @param array $headers
     * @return JsonResponse
     */
    public static function SuccessResponse(string $message = 'Successful Action', array $headers = []): JsonResponse
    {
        return Response()->json([
            'message' => ucfirst($message),
            'status' => 200,
        ], 200)->withHeaders($headers);
    }
}

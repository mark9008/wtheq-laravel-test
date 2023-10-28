<?php

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;
/**
 * Class APIResponse handles all API responses
 *
 * @package App\Http\Responses
 */
class APIResponse
{
/**
 * Login Response representation
 *
 * @param $token
 * @param User $user
 * @param array $headers
 * @return JsonResponse
 */
    public static function LoginResponse($token, User $user, array $headers = []): JsonResponse
    {
        $responseBody = [
            'data' => UserResource::make($user),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,

        ];
        return Response()->json($responseBody)->withHeaders($headers);
    }

    /**
     * Logout Response representation
     *
     * @param array $headers
     * @return JsonResponse
     */
    public static function LogoutResponse(array $headers = []): JsonResponse
    {
        $responseBody = [
            'message' => 'Logged out successfully',
            'status' => 200,
        ];
        return Response()->json($responseBody)->withHeaders($headers);
    }

    /**
     * Error Response representation
     *
     * @param string $title
     * @param string $detail
     * @param Throwable|null $exception
     * @param int $status
     * @param array $headers
     * @param bool $logError
     * @return JsonResponse
     */
    public static function ErrorsResponse(string $title, string $detail, throwable $exception = null, int $status = Response::HTTP_BAD_REQUEST, array $headers = [], bool $logError = false): JsonResponse
    {
        if ($logError) // to check if the error should be logged or not
        {
            // log the error
            if ($exception?->getCode() === 0 && !$exception instanceof ModelNotFoundException) {
                \Log::error(
                    "status code: {$exception->getCode()}\n\n" .
                    "file: {$exception->getFile()}\n\n" .
                    "line: {$exception->getLine()}\n\n" .
                    "message: {$exception->getMessage()}\n\n" .
                    $exception->getTraceAsString()
                );
            }
        }
        // define error array
        $errorArr = array();

        // check if the error says that there is no query results for model
        if (str_contains($detail, 'No query results for model')) {
            $detail = "Provided Data was not found";
            // should return 404 status code
            $status = Response::HTTP_NOT_FOUND;
        }

        // check if it is a DB error
        if (str_contains($detail, 'SQLSTATE')) {
            $errorArr['DBError'] = $detail;
            $detail = 'Oops! Please refresh your page. Thanks for your patience!"';
        }

        // merge error array with the error and other details
        $errorArr = array_merge($errorArr, [
            "title" => $title,
            "details" => ucfirst($detail),
            'status' => $status,
        ]);

        // return the JSON response
        $responseBody = [
            'errors' => $errorArr,
        ];
        return Response()->json($responseBody, $status)->withHeaders($headers);
    }

    /**
     *  Created Successfully Response representation
     *
     * @param $data
     * @param string $message
     * @param array $additional
     * @param array $headers
     * @return JsonResponse
     */
    public static function CreatedSuccessfully($data, string $message = 'created successfully', array $additional = [], array $headers = []): JsonResponse
    {
        // returns the data of the created model fetched from the resource class
        $responseBody = [
            'data' => $data,
            'message' => $message,
            'status' => 201,
        ];

        // check if there is additional data to be shown in the response
        if (!empty($additional))
            $responseBody['additional'] = $additional;

        // return the JSON response
        return Response()->json($responseBody, Response::HTTP_CREATED)->withHeaders($headers);
    }

    /**
     *  Data Response representation
     *
     * @param $data
     * @param array $additional
     * @param array $headers
     * @return JsonResponse
     */
    public static function DataResponse($data, array $additional = [], array $headers = []): JsonResponse
    {
        // returns the data of the model fetched from the resource class
        $responseBody = [
            'data' => $data,
            'status' => 200,
        ];

        // check if there is additional data to be shown in the response
        if (!empty($additional))
            $responseBody['additional'] = $additional;

        // return the JSON response
        return Response()->json($responseBody)->withHeaders($headers);
    }

    /**
     *  Updated Successfully Response representation
     *
     * @param $model
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    public static function UpdatedSuccessfully($model, string $message = 'updated successfully', array $headers = []): JsonResponse
    {
        $responseBody = [
            'data' => $model,
            'message' => ucfirst($message),
            'status' => 200,
        ];
        return Response()->json($responseBody)->withHeaders($headers);
    }

    /**
     * @param $message
     * @param array $headers
     * @return JsonResponse
     */
    public static function SuccessResponse(string $message = 'Successful Action', array $headers = []): JsonResponse
    {
        $responseBody = [
            'message' => ucfirst($message),
            'status' => 200,
        ];
        return Response()->json($responseBody)->withHeaders($headers);
    }
}

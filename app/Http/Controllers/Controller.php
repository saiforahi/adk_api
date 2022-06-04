<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @param mixed|null $data
     * @param string $message
     * @param int $status
     * @param bool $success
     * @return JsonResponse
     */
    public function success(mixed $data = null, string $message = "", int $status = 200, bool $success = true): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if(isset($data->resource) && $data->resource instanceof AbstractPaginator) {
            $data = $data->resource->toArray();
        } else if(!($data instanceof LengthAwarePaginator)) {
            $data = compact('data');
        }else{
            $data = $data->toArray();
        }

        $response += $data;

        return new JsonResponse($response, $status);
    }

    /**
     * Send a failure response
     * @param null $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public function failed($data = null, string $message = "", int $status = 200): JsonResponse
    {
        return $this->success($data, $message, $status, false);
    }
}

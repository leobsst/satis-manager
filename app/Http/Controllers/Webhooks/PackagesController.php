<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Webhooks\BuildRequest;
use App\Jobs\BuildPackages;
use App\Models\Repository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Log;

class PackagesController extends Controller
{
    public function build(BuildRequest $request): JsonResponse
    {
        $data = $request->validated();

        Log::channel('webhooks')->info('Received package build webhook', $data);

        try {
            $repository = Repository::where('url', $data['source_repo'])
                ->where('provider', $data['provider'])
                ->firstOrFail();

            BuildPackages::dispatch($repository);
        } catch (Exception $e) {
            $statusCode = 400;
            $message = 'Error processing package build webhook';

            if ($e instanceof ModelNotFoundException) {
                $message = 'No matching repository found for webhook';
                $statusCode = 404;
                Log::channel('webhooks')->warning($message, $data);
            } else {
                Log::channel('webhooks')->error($message, [
                    'exception' => $e,
                    'data' => $data,
                ]);
            }

            return response()->json(ApiHelper::getErrorResponseArray([
                'error' => $e->getMessage(),
                'message' => $message,
            ]), $statusCode);
        }

        return response()->json(['status' => 'success']);
    }
}

<?php

namespace Kellton\Tools\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;
use Kellton\Tools\Features\Dependency\Traits\UseDependency;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Controller adds additional functionality to all controllers.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, UseDependency;

    /**
     * Perform a controller action.
     *
     * @param callable $action
     * @param callable|null $beforeAction Operations to be performed before the action. Falls back on class method.
     * @param callable|null $onActionException Operations to be performed when an exception is caught. Falls back on
     *     class method.
     * @param callable|null $afterAction Operations to be performed after the action. Falls back on class method.
     *
     * @return JsonResponse|View
     */
    protected function action(
        callable $action,
        ?callable $beforeAction = null,
        ?callable $onActionException = null,
        ?callable $afterAction = null
    ): JsonResponse|View {
        try {
            $beforeAction ? $beforeAction() : $this->beforeAction();

            $result = $action();

            return $this->convertToResponse($result);
        } catch (Throwable $exception) {
            try {
                $onActionException ? $onActionException($exception) : $this->onActionException($exception);

                $result = $this->convertToResponseFromException($exception);
            } catch (Throwable $innerException) {
                $result = $this->convertToResponseFromException($exception);
            }

            report($exception);
        } finally {
            $afterAction ? $afterAction() : $this->afterAction();
        }

        return $result;
    }

    /**
     * Operations to be performed before a controller action.
     */
    protected function beforeAction(): void
    {
    }

    /**
     * Operations to be performed after a controller action.
     */
    protected function afterAction(): void
    {
    }

    /**
     * Operations to be performed when an exception is caught during a controller action.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    protected function onActionException(Throwable $exception): void
    {
    }

    /**
     * Return a correct response based on the result of the action.
     *
     * @param mixed $result
     *
     * @return JsonResponse|View
     */
    private function convertToResponse(mixed $result): JsonResponse|View
    {
        return $result instanceof View ? $result : response()->json($result);
    }

    private function convertToResponseFromException(Throwable $exception): JsonResponse
    {
        $code = $exception->getCode();
        if (property_exists($exception, 'status')) {
            $code = $exception->status;
        }

        $statusCode = !array_key_exists($code, Response::$statusTexts)
            ? Response::HTTP_INTERNAL_SERVER_ERROR
            : $code;

        $response = collect([
            'status' => $statusCode,
            'message' => $exception->getMessage(),
        ]);

        return response()->json($response, $statusCode);
    }
}

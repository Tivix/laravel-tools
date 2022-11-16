<?php

namespace Kellton\Tools\Features\Action\Services;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Kellton\Tools\Exceptions\NotFound;
use Kellton\Tools\Features\Action\Data\FailResult;
use Kellton\Tools\Features\Action\Data\Result;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Validator;

/**
 * Class Service handles services logic for using actions.
 */
abstract class ActionService extends Service
{
    /**
     * @var mixed|null $object Loaded object.
     */
    protected mixed $object = null;

    /**
     * Load object.
     *
     * @param mixed $object
     *
     * @return static
     */
    public function load(mixed $object): static
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Unload object.
     *
     * @return static
     */
    public function unload(): static
    {
        $this->object = null;

        return $this;
    }

    /**
     * Get loaded object.
     *
     * @return mixed
     */
    protected function getObject(): mixed
    {
        return $this->object;
    }

    /**
     * Perform a service action that does not require a loaded object.
     *
     * @param callable $action if an array of callables is provided, they are executed in separate transactions until
     *     the first unsuccessful result.
     * @param callable|null $policy a callable that enforces a policy is expected
     * @param callable|null $validation called before performing the action.
     * @param callable|null $view a callable that processes the Result via a View
     * @param callable|null $beforeAction operations to be performed before the action. Falls back on class method.
     * @param callable|null $onActionSuccess operations to be performed when the action is successful. Falls back on
     *     class method.
     * @param callable|null $onActionException operations to be performed when an exception is caught. Falls back on
     *     class method.
     * @param callable|null $afterAction operations to be performed after the action. Falls back on class method.
     *
     * @return mixed
     */
    protected function action(
        callable $action,
        ?callable $policy = null,
        ?callable $validation = null,
        ?callable $view = null,
        ?callable $beforeAction = null,
        ?callable $onActionSuccess = null,
        ?callable $onActionException = null,
        ?callable $afterAction = null,
    ): mixed {
        return $this->executeAction(
            requiresLoadedObject: false,
            action: $action,
            policy: $policy,
            validation: $validation,
            view: $view,
            beforeAction: $beforeAction,
            onActionSuccess: $onActionSuccess,
            onActionException: $onActionException,
            afterAction: $afterAction
        );
    }

    /**
     * Perform a service action that requires a loaded object.
     *
     * @param callable $action if an array of callables is provided, they are executed in separate
     *        transactions until the first unsuccessful result.
     * @param callable|null $policy a callable that enforces a policy is expected
     * @param callable|null $validation called before performing the action.
     * @param callable|null $view a callable that processes the Result via a View
     * @param callable|null $beforeAction operations to be performed before the action. Falls back on class method.
     * @param callable|null $onActionSuccess operations to be performed when the action is successful. Falls back on
     *     class method.
     * @param callable|null $onActionException operations to be performed when an exception is caught. Falls back on
     *     class method.
     * @param callable|null $afterAction operations to be performed after the action. Falls back on class method.
     *
     * @return mixed
     */
    protected function actionOnObject(
        callable $action,
        ?callable $policy = null,
        ?callable $validation = null,
        ?callable $view = null,
        ?callable $beforeAction = null,
        ?callable $onActionSuccess = null,
        ?callable $onActionException = null,
        ?callable $afterAction = null
    ): mixed {
        return $this->executeAction(
            requiresLoadedObject: true,
            action: $action,
            policy: $policy,
            validation: $validation,
            view: $view,
            beforeAction: $beforeAction,
            onActionSuccess: $onActionSuccess,
            onActionException: $onActionException,
            afterAction: $afterAction
        );
    }

    /**
     * Operations to be performed before a service action.
     */
    protected function beforeAction(): void
    {
    }

    /**
     * Operations to be performed after a service action.
     */
    protected function afterAction(): void
    {
    }

    /**
     * Operations to be performed when an exception is caught during a service action.
     *
     * @param Throwable $exception
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    protected function onActionException(Throwable $exception): void
    {
    }

    /**
     * Operations to be performed when a service action completes successfully.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    protected function onActionSuccess(Result $result): void
    {
    }

    /**
     * Validate loaded object.
     *
     * @throws NotFound
     */
    protected function validateLoadedObject(): void
    {
        if (!isset($this->object)) {
            throw new NotFound('Object not loaded.');
        }
    }

    /**
     * Validation wrapper.
     *
     * @param array $fields
     * @param array $rules
     * @param array $message
     * @param array $customAttributes
     *
     * @return void
     *
     * @throws ValidationException when validation fails
     */
    protected function validate(array $fields, array $rules, array $message = [], array $customAttributes = []): void
    {
        foreach ($fields as &$field) {
            if ($field instanceof Collection) {
                $field = $field->toArray();
            }

            if ($field instanceof Carbon) {
                $field = $field->format(config('tools.date.datetime_format'));
            }
        }
        unset($field);

        Validator::make($fields, $rules, $message, $customAttributes)->validate();
    }

    /**
     * Return result based on the exception.
     *
     * @param Throwable $exception
     *
     * @return FailResult
     */
    protected function createResultFromException(Throwable $exception): FailResult
    {
        $code = $exception->getCode();
        if (property_exists($exception, 'status')) {
            $code = $exception->status;
        }

        if (!array_key_exists($code, Response::$statusTexts)) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        } else {
            $statusCode = $code;
        }

        return new FailResult(
            code: $statusCode,
            message: $exception->getMessage(),
        );
    }

    /**
     * Perform a service action.
     *
     * @param bool $requiresLoadedObject whether this action requires an object to be loaded.
     * @param callable $action if an array of callables is provided, they are executed in separate transactions until
     *     the first unsuccessful result.
     * @param callable|null $policy a callable that enforces a policy
     * @param callable|null $validation called before performing the action.
     * @param callable|null $view a callable that processes the Result via a View
     * @param callable|null $beforeAction operations to be performed before the action. Falls back on class method.
     * @param callable|null $onActionSuccess operations to be performed when the action is successful. Falls back on
     *     class method.
     * @param callable|null $onActionException operations to be performed when an exception is caught. Falls back on
     *     class method.
     * @param callable|null $afterAction operations to be performed after the action. Falls back on class method.
     *
     * @return mixed
     */
    private function executeAction(
        bool $requiresLoadedObject,
        callable $action,
        ?callable $policy = null,
        ?callable $validation = null,
        ?callable $view = null,
        ?callable $beforeAction = null,
        ?callable $onActionSuccess = null,
        ?callable $onActionException = null,
        ?callable $afterAction = null
    ): mixed {
        try {
            $beforeAction ? $beforeAction() : $this->beforeAction();

            if ($requiresLoadedObject) {
                // Check loaded object
                $this->validateLoadedObject();
            }

            // Policy
            if ($policy !== null) {
                $policy();
            }

            // Validation
            if ($validation !== null) {
                $validation();
            }

            // Action
            $result = DB::transaction(static function () use ($action) {
                return $action();
            });

            if ($result instanceof Result) {
                $onActionSuccess ? $onActionSuccess($result) : $this->onActionSuccess($result);
            }
        } catch (Throwable $exception) {
            try {
                $onActionException ? $onActionException($exception) : $this->onActionException($exception);

                $result = $this->createResultFromException($exception);
            } catch (Throwable $innerException) {
                $result = $this->createResultFromException($innerException);
            }

            report($exception);
        } finally {
            $afterAction ? $afterAction() : $this->afterAction();
        }

        if ($view !== null) {
            return $view($result);
        }

        return $result;
    }
}

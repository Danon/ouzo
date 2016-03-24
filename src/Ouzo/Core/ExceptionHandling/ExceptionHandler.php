<?php
namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Routing\RouterException;
use Ouzo\UserException;

class ExceptionHandler
{
    private static $errorHandled = false;
    public static $errorRenderer = null;

    public function handleException($exception)
    {
        if ($exception instanceof UserException) {
            $this->renderUserError(OuzoExceptionData::forException(500, $exception));
        } elseif ($exception instanceof RouterException) {
            $this->renderNotFoundError(OuzoExceptionData::forException(404, $exception));
        } elseif ($exception instanceof OuzoException) {
            $this->handleError($exception->asExceptionData());
        } else {
            $this->handleError(OuzoExceptionData::forException(500, $exception));
        }
    }

    public function handleExceptionData(OuzoExceptionData $exceptionData)
    {
        $this->handleError($exceptionData);
    }

    public static function lastErrorHandled()
    {
        return self::$errorHandled;
    }

    private function handleError($exception)
    {
        $this->renderError($exception);
    }

    private function renderUserError($exception)
    {
        header("Contains-Error-Message: User");
        $this->renderError($exception, 'user_exception');
    }

    private function renderNotFoundError($exception)
    {
        $this->renderError($exception);
    }

    private function renderError(OuzoExceptionData $exceptionData, $viewName = 'exception')
    {
        try {
            $renderer = self::$errorRenderer ?: new ErrorRenderer();
            $renderer->render($exceptionData, $viewName);
            self::$errorHandled = true;
        } catch (Exception $e) {
            echo "Framework critical error. Exception thrown in exception handler.<br>\n";
            echo "<hr>\n";
            echo "Message: " . $e->getMessage() . "<br>\n";
            echo "Trace: " . $e->getTraceAsString() . "<br>\n";
        }
    }
}
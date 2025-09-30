<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationException extends HttpException
{
    public function __construct(string|ConstraintViolationListInterface $errors, int $statusCode = 422)
    {
        $detail = is_string($errors) ? $errors : (string)$errors;
        parent::__construct($statusCode, $detail);
    }

    public static function toJson(\Throwable $e): JsonResponse
    {
        return new JsonResponse([
            'title'  => 'Validation Failed',
            'status' => $e->getCode() ?: 422,
            'detail' => $e->getMessage(),
        ], $e->getCode() ?: 422);
    }
}

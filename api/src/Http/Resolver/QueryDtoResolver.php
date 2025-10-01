<?php
declare(strict_types=1);

namespace App\Http\Resolver;

use App\Http\Attribute\FromQuery;
use App\Exception\ValidationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ReflectionClass;
use ReflectionNamedType;

final class QueryDtoResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attr = $this->getAttribute($argument);
        if (!$attr) {
            return [];
        }

        $class = $argument->getType();
        if (!$class || !class_exists($class)) {
            return [];
        }

        $payload = $attr->defaults + $request->query->all();
        $payload = $this->coerceScalarsToPropertyTypes($payload, $class);

        $dto = $this->serializer->denormalize($payload, $class);

        $violations = $this->validator->validate($dto);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        return [$dto];
    }

    private function getAttribute(ArgumentMetadata $argument): ?FromQuery
    {
        foreach ($argument->getAttributes(FromQuery::class, ArgumentMetadata::IS_INSTANCEOF) as $attribute) {
            return $attribute;
        }
        return null;
    }

    private function coerceScalarsToPropertyTypes(array $payload, string $class): array
    {
        $rc = new ReflectionClass($class);
        $types = [];
        foreach ($rc->getProperties() as $prop) {
            $t = $prop->getType();
            if ($t instanceof ReflectionNamedType) {
                $types[$prop->getName()] = $t->getName();
            }
        }

        foreach ($payload as $key => $value) {
            if (!array_key_exists($key, $types)) {
                continue;
            }
            $type = $types[$key];
            if (is_string($value)) {
                switch ($type) {
                    case 'int':
                        if ($value !== '') { $payload[$key] = (int) $value; }
                        break;
                    case 'float':
                    case 'double':
                        if ($value !== '') { $payload[$key] = (float) $value; }
                        break;
                    case 'bool':
                        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        if ($bool !== null) { $payload[$key] = $bool; }
                        break;
                }
            }
        }

        return $payload;
    }
}

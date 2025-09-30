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

        /** @var object $dto */
        $dto = $this->serializer->deserialize(json_encode($payload, JSON_THROW_ON_ERROR), $class, 'json');

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
}

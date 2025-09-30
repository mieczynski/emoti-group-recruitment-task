<?php
declare(strict_types=1);

namespace App\Http\Resolver;

use App\Http\Attribute\FromBody;
use App\Exception\ValidationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BodyDtoResolver implements ValueResolverInterface
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

        $json = $request->getContent() ?: '{}';
        /** @var object $dto */
        $dto = $this->serializer->deserialize($json, $class, 'json');
        $violations = $this->validator->validate($dto);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        return [$dto];
    }

    private function getAttribute(ArgumentMetadata $argument): ?FromBody
    {
        foreach ($argument->getAttributes(FromBody::class, ArgumentMetadata::IS_INSTANCEOF) as $attribute) {
            return $attribute;
        }
        return null;
    }
}

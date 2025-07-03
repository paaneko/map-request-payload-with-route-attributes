<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapRequestPayloadWithRouteContext extends MapRequestPayload
{
    public function __construct(
        public ?array $routeParamsValidationMap = null,
        array|string|null $acceptFormat = null,
        array $serializationContext = [],
        array|GroupSequence|string|null $validationGroups = null,
        string $resolver = RequestPayloadValueResolver::class,
        int $validationFailedStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?string $type = null
    ) {
        parent::__construct(
            $acceptFormat,
            $serializationContext,
            $validationGroups,
            $resolver,
            $validationFailedStatusCode,
            $type
        );
    }

}


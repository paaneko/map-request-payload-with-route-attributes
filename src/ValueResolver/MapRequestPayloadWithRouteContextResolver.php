<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsDecorator('argument_resolver.request_payload')]
readonly class MapRequestPayloadWithRouteContextResolver implements ValueResolverInterface, EventSubscriberInterface
{
    public function __construct(
        #[AutowireDecorated]
        private RequestPayloadValueResolver $inner,
        private ValidatorInterface $validator,
        private PropertyAccessorInterface $propertyAccessor
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        return $this->inner->resolve($request, $argument);
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $argumentAttributes = $event->getArguments();

        $this->inner->onKernelControllerArguments($event);

        if (empty($routeParams = $event->getRequest()->attributes->get('_route_params', []))) {
            return;
        }

        foreach ($argumentAttributes as $i => $argumentAttribute) {
            if ($argumentAttribute::class === MapRequestPayloadWithRouteContext::class) {
                // On that point object would be resolved by inner resolver, so we take him
                $resolvedObject = $event->getArguments()[$i];

                if (
                    $argumentAttribute->routeParamsValidationMap !== null
                    && ($violations = $this->validator->validate(
                        $routeParams,
                        new Collection(fields: $argumentAttribute->routeParamsValidationMap)
                    ))->count()
                ) {
                    throw new ValidationFailedException($routeParams, $violations);
                }

                foreach ($routeParams as $routeParamName => $routeParamValue) {
                    if ($this->propertyAccessor->isWritable($resolvedObject, $routeParamName)) {
                        $this->propertyAccessor->setValue($resolvedObject, $routeParamName, $routeParamValue);
                    }
                }
            }
        }
    }


    public static function getSubscribedEvents(): array
    {
        return RequestPayloadValueResolver::getSubscribedEvents();
    }
}


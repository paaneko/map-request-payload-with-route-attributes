<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\ExampleRequest;
use App\ValueResolver\MapRequestPayloadWithRouteContext;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\Length;
use OpenApi\Attributes as OA;

#[Route(path: '/api/example/{project}', methods: 'POST')]
#[Tag('Example')]
#[OA\Response(
    response: 200,
    description: 'Returns the example response',
    content: new Model(
        type: ExampleRequest::class,
        serializationContext: ['read']
    )
)]
class ExampleController extends AbstractController
{
    public function __invoke(
        #[MapRequestPayloadWithRouteContext(
            routeParamsValidationMap: [
                'project' => new Length(max: 10)
            ],
            serializationContext: [AbstractNormalizer::GROUPS => ['write']]
        )]
        ExampleRequest $exampleRequest,
        #[MapRequestPayload]
        ExampleRequest $exampleRequestTwo,
    ): Response {
        return $this->json(
            [
                '🔥' => $exampleRequest,
                '💩' => $exampleRequestTwo
            ],
            context: [AbstractNormalizer::GROUPS => ['read']]
        );
    }
}

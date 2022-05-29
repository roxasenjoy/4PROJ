<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Contact;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param array<mixed> $context
     */
    public function __invoke(array $context = []): OpenApi
    {

        $openApi = ($this->decorated)($context);

        $schemas = $openApi->getComponents()->getSecuritySchemes();

        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);

        #Sécurité
        $openApi = $openApi->withSecurity(['cookieAuth']);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'andrew@supinfo.com'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '123456',
                ]
            ]
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string'
                ]
            ]
        ]);


        /**
         * Enlever le paramètre ID du /api/me
         */
        $meOperation = $openApi->getPaths()->getPath('/api/v1/user')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/v1/user')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/v1/user', $mePathItem);

        /**
         * Login
         */
        $pathItem = new PathItem(
            post: new Operation(

                operationId: 'postApilogin',
                tags: ['Auth'],

                responses: [
                    '200' => [
                        'description' => 'Token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],

                requestBody: new RequestBody(
                  content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Credentials'
                                ]
                            ]
                        ]
                    )
                )
            )
        );

        $openApi->getPaths()->addPath('/api/login', $pathItem);

        /**
         * Logout
         */
        $pathItem = new PathItem(
            post: new Operation(

                operationId: 'postApiLogout',
                tags: ['Auth'],

                responses: [
                    '204' => []
                ],
            )
        );

        $openApi->getPaths()->addPath('/logout', $pathItem);


        return $openApi;
    }
}
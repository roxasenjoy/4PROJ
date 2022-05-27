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

        $schemas['cookieAuth'] = new \ArrayObject([
            'type' => 'apiKey',
            'in' => 'cookie',
            'name' => 'PHPSESSID'
        ]);

        #Sécurité
     //   $openApi = $openApi->withSecurity(['cookieAuth']);

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


        /**
         * Enlever le paramètre ID du /api/me
         */
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);

        /**
         * Login
         */
        $pathItem = new PathItem(
            post: new Operation(

                operationId: 'postApilogin',
                tags: ['Auth'],

                responses: [
                    '200' => [
                        'description' => 'Utilisateur connecté',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-read.User'
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
<?php

use App\Cache\FilesystemCache\FilesystemCacheFactory;
use App\Cache\FilesystemCache\FilesystemCacheFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TheCodingMachine\GraphQLite\Http\Psr15GraphQLMiddlewareBuilder;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Tuupola\Middleware\CorsMiddleware;

const DS = DIRECTORY_SEPARATOR;

return [
    // Implements PSR-16
    FilesystemCacheFactoryInterface::class => DI\create(FilesystemCacheFactory::class)->constructor(APP_DIRECTORY . DS . ".." . DS . "var" . DS . "cache"),
    ResponseFactoryInterface::class => DI\autowire(Psr17Factory::class),
    // TheCodingMachine, implements PSR-15
    WebonyxGraphqlMiddleware::class => DI\factory(function (
        ContainerInterface              $container,
        FilesystemCacheFactoryInterface $filesystemCacheFactory,
        Psr17Factory $psr17Factory
    ) {
        $filesystemCache = $filesystemCacheFactory->create("graphql");

        $schemaFactory = new SchemaFactory($filesystemCache, $container);

        $schemaFactory->addControllerNamespace("App\\GraphQL")
            ->addTypeNamespace("whatever")
            ->addTypeNamespace("App\\GraphQL\\");

        $schema = $schemaFactory->createSchema();

        $builder = new Psr15GraphQLMiddlewareBuilder($schema);
        $builder->setUrl("/graphql");
        $builder->setResponseFactory($psr17Factory);
        $builder->setStreamFactory($psr17Factory);

        return $builder->createMiddleware();
    }),
    // PSR-7
    ResponseInterface::class => DI\factory(function (ResponseFactoryInterface $responseFactory) {
        return $responseFactory->createResponse();
    }),
    // PSR-15
    ServerRequestInterface::class => DI\factory(function (
        ClientInterface               $httpClient,
        Psr17Factory $psr17Factory
    ) {
        $creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        $serverRequest = $creator->fromGlobals();

        $contentType = $serverRequest->getHeaderLine("Content-Type");

        // Parse request body, because Nyholm\Psr7Server doesn't parse JSON requests
        if ($contentType === "application/json") {
            if (!$serverRequest->getParsedBody()) {
                $content = $serverRequest->getBody()->getContents();
                $data = json_decode($content, true);

                if ($data === false || json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException(json_last_error_msg() . " in body: '" . $content . "'");
                }

                $serverRequest = $serverRequest->withParsedBody($data);
            }
        }

        return $serverRequest;
    }),
    // Guzzle
    ClientInterface::class => DI\autowire(GuzzleHttp\Client::class),
    CorsMiddleware::class => DI\factory(function () {
        return new CorsMiddleware([
            "origin" => ["http://graphql-debug.local"],
            "methods" => ["GET", "POST"],
            "headers.allow" => ["Content-Type"],
            "headers.expose" => ["Content-Type"],
            "credentials" => true,
            "cache" => 0
        ]);
    }),
];

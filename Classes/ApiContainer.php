<?php

use Slim\Container;

/**
 * Class ApiContainer
 * @package Classes
 */
class ApiContainer
{
    const CONFIGURATION_DISPLAY_ERROR_DETAILS = true;

    public function __construct()
    {
    }

    static function getConfiguration()
    {
        $configuration = [
            'settings' => [
                'displayErrorDetails' => self::CONFIGURATION_DISPLAY_ERROR_DETAILS,
            ],
        ];

        return $configuration;
    }

    static function getContainer()
    {
        $container = new Container(self::getConfiguration());

        //Override the default Not Found Handler
        $container['notFoundHandler'] = function ($container) {
            return function ($request, $response) use ($container) {
                // Generate responses for the following general errors
                $apiResponses = new ApiResponses();
                $apiResponses->setStatusCode(ApiResponses::HTTP_404_CODE);
                $apiResponses->setMessage(ApiResponses::HTTP_404_MESSAGE);
                $toJson = $apiResponses->getToJsonArray();

                return $container['response']
                    ->withJson($toJson, ApiResponses::HTTP_404_CODE);
            };
        };

        $container['Api'] = function ($container) {
            return new Api($container->get('settings'));
        };

        $container['errorHandler'] = function ($container) {
            return function ($request, $response, $exception) use ($container) {
                // Generate responses for the following general errors
                $apiResponses = new ApiResponses();
                $apiResponses->setStatusCode(ApiResponses::HTTP_500_CODE);
                $apiResponses->setCode(ApiResponses::UNDEFINED_ERROR);
                $apiResponses->setMessage(ApiResponses::HTTP_500_MESSAGE);
                $toJson = $apiResponses->getToJsonArray();

                return $container['response']
                    ->withJson($toJson, ApiResponses::HTTP_500_CODE);
            };
        };

        $container['notAllowedHandler'] = function ($container) {
            return function ($request, $response, $exception) use ($container) {
                // Generate responses for the following general errors
                $apiResponses = new ApiResponses();
                $apiResponses->setStatusCode(ApiResponses::HTTP_401_CODE);
                $apiResponses->setMessage(ApiResponses::HTTP_401_MESSAGE);
                $toJson = $apiResponses->getToJsonArray();

                return $container['response']
                    ->withJson($toJson, ApiResponses::HTTP_401_CODE);
            };
        };

        $container['phpErrorHandler'] = function ($container) {
            return function ($request, $response, $error) use ($container) {
                // Generate responses for the following general errors
                $apiResponses = new ApiResponses();
                $apiResponses->setStatusCode(ApiResponses::HTTP_401_CODE);
                $apiResponses->setMessage(ApiResponses::HTTP_401_MESSAGE);
                $toJson = $apiResponses->getToJsonArray();

                return $container['response']
                    ->withJson($toJson, ApiResponses::HTTP_401_CODE);
            };
        };

        return $container;
    }
}
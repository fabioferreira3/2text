<?php

namespace App\Adapters;

use InvalidArgumentException;

/**
 * @codeCoverageIgnore
 */
class ImageGeneratorHandler
{
    protected array $handlers;

    public function __construct()
    {
        $this->handlers = [
            'StabilityAI' => 'App\Adapters\StabilityAIHandler',
            'DallE' => 'App\Adapters\DallEHandler',
        ];
    }

    public function handle(string $action, array $params)
    {
        if (!in_array($action, ['textToImage', 'imageToImage'])) {
            throw new InvalidArgumentException("The handler for the specified action does not exist.");
        }

        $handlerClass = $action === 'textToImage' ? $this->handlers['DallE'] : $this->handlers['StabilityAI'];
        $handler = new $handlerClass();

        if (!method_exists($handler, $action)) {
            throw new InvalidArgumentException("The specified action is not supported by the handler class.");
        }

        return $handler->$action($params);
    }
}

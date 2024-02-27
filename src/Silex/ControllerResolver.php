<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Silex;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds Application as a valid argument for controllers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ControllerResolver extends BaseControllerResolver
{
    protected $app;

    /**
     * Constructor.
     *
     * @param Application     $app    An Application instance
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(Application $app, LoggerInterface $logger = null)
    {
        $this->app = $app;

        parent::__construct($logger);
    }

    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        foreach ($parameters as $param) {
            if (PHP_VERSION_ID >= 80000) {
                if (((string)$param->getType()) && is_a($this->app, (string)$param->getType())) {
                    $request->attributes->set($param->getName(), $this->app);

                    break;
                }
            }

            if (PHP_VERSION_ID < 80000) {
                if ($param->getClass() && $param->getClass()->isInstance($this->app)) {
                    $request->attributes->set($param->getName(), $this->app);

                    break;
                }
            }
        }

        return parent::doGetArguments($request, $controller, $parameters);
    }
}

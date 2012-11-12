<?php

namespace Pomm\PommBundle\Security;

use Symfony\Component\DependencyInjection\ContainerAware;

class PommManager extends ContainerAware
{
    private $connections;

    public function __construct(array $connections)
    {
        $this->connections = $connections;
    }

    public function getConnection($name)
    {
        return $this->container->get($this->connections[$name]);
    }
}

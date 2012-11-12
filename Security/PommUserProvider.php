<?php

namespace Pomm\PommBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class PommUserProvider implements UserProviderInterface
{
    private $pomm;

    public function __construct(PommManager $pomm)
    {
        $this->pomm = $pomm;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {

        $connection = $this->pomm->getConnection($this->options['connection_name']);
        $map        = $connection->getMapFor($this->options['user_class']);
        $column     = $this->options['username_column'];

        if (null !== $column) {
            $user = $map->findWhere(sprintf('%s = ?', $column), array($username))->current();
            if (null === $user) {
                throw new UsernameNotFoundException();
            }

            return $user;
        } elseif ($map instanceof UserProviderInterface) {
            return $map->loadUserByUsername($username);
        }

        throw \Exception();// map ...
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->options['user_class']) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->options['user_class'];
    }
}

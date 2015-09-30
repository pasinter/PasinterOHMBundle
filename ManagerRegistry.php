<?php

namespace Pasinter\Bundle\RedisOHMBundle;

use Pasinter\OHM\Exception\OHMException;
use Symfony\Bridge\Doctrine\ManagerRegistry as BaseManagerRegistry;

class ManagerRegistry extends BaseManagerRegistry
{
    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * @param string $alias
     * @return string
     * @throws OHMException
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (OHMException $e) {
            }
        }
        throw OHMException::unknownEntityNamespace($alias);
    }
}


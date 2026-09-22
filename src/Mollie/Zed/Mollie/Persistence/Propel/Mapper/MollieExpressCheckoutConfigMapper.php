<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

class MollieExpressCheckoutConfigMapper implements MollieExpressCheckoutConfigMapperInterface
{
    /**
     * @param iterable<\Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutConfig> $entities
     *
     * @return array<string, bool>
     */
    public function mapEntitiesToMethodConfig(iterable $entities): array
    {
        $methodConfig = [];
        foreach ($entities as $entity) {
            $methodConfig[$entity->getExpressMethod()] = (bool)$entity->getIsActive();
        }

        return $methodConfig;
    }
}

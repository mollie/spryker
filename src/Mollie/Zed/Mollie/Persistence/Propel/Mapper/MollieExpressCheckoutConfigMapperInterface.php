<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

interface MollieExpressCheckoutConfigMapperInterface
{
    /**
     * @param iterable<\Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutConfig> $entities
     *
     * @return array<string, bool>
     */
    public function mapEntitiesToMethodConfig(iterable $entities): array;
}

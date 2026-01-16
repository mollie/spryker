<?php

namespace Mollie\Client\Mollie;

interface TestUrlBuilderInterface
{
    /**
     * @return string
     */
    public function buildWebhookUrl(): string;
}

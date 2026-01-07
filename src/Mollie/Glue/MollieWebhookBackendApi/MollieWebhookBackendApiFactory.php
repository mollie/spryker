<?php

namespace Mollie\Glue\MollieWebhookBackendApi;

use Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface;
use Mollie\Glue\MollieWebhookBackendApi\Processor\WebhookProcessor;
use Mollie\Glue\MollieWebhookBackendApi\Processor\WebhookProcessorInterface;
use Spryker\Glue\Kernel\Backend\AbstractBackendApiFactory;

class MollieWebhookBackendApiFactory extends AbstractBackendApiFactory
{
    /**
     * @return \Mollie\Glue\MollieWebhookBackendApi\Processor\WebhookProcessorInterface
     */
    public function createWebhookProcessor(): WebhookProcessorInterface
    {
        return new WebhookProcessor(
            $this->getMollieClient(),
            $this->getMollieFacade(),
        );
    }

    /**
     * @return \Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface
     */
    public function getMollieClient(): MollieWebhookBackendApiToMollieClientInterface
    {
        return $this->getProvidedDependency(MollieWebhookBackendApiDependencyProvider::CLIENT_MOLLIE);
    }

    /**
     * @return \Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface
     */
    public function getMollieFacade(): MollieWebhookBackendApiToMollieFacadeInterface
    {
        return $this->getProvidedDependency(MollieWebhookBackendApiDependencyProvider::FACADE_MOLLIE);
    }
}

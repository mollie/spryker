<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Plugin\MollieWebhookBackendApi;

use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Glue\MollieWebhookBackendApi\Controller\MollieWebhookResourceController;
use Spryker\Glue\GlueApplication\Plugin\GlueApplication\Backend\AbstractResourcePlugin;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

class MollieWebhookBackendResourcePlugin extends AbstractResourcePlugin implements ResourceInterface
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'MollieWebhook';
    }

    /**
     * @inheritDoc
     */
    public function getController(): string
    {
        return MollieWebhookResourceController::class;
    }

    /**
     * @inheritDoc
     */
    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return (new GlueResourceMethodCollectionTransfer())
            ->setPost(
                (new GlueResourceMethodConfigurationTransfer())
                    ->setAction('postWebhookAction')
                    ->setAttributes(MollieApiRequestTransfer::class),
            );
    }
}

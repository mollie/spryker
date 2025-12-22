<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Processor;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface;
use Symfony\Component\HttpFoundation\Response;

class WebhookProcessor implements WebhookProcessorInterface
{
    /**
     * @param \Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface $mollieClient
     * @param \Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface $mollieFacade
     */
    public function __construct(
        protected MollieWebhookBackendApiToMollieClientInterface $mollieClient,
        protected MollieWebhookBackendApiToMollieFacadeInterface $mollieFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function processWebhook(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $glueResponseTransfer = new GlueResponseTransfer();

        $content = $glueRequestTransfer->getContent();
        $data = json_decode($content, true);

        if (!isset($data['id'])) {
            return $glueResponseTransfer
                ->setHttpStatus(Response::HTTP_OK)
                ->setContent('Missing payment ID');
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setBody($data);

        $updateOrderCollectionRequestTransfer = $this->mollieClient->getPaymentById($mollieApiRequestTransfer);

        $this->mollieFacade->updateOrderCollection($updateOrderCollectionRequestTransfer);

        return $glueResponseTransfer
            ->setHttpStatus(Response::HTTP_OK)
            ->setContent('OK');
    }
}

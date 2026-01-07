<?php


declare(strict_types = 1);

namespace Mollie\Glue\MollieWebhookBackendApi\Processor;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface;
use Mollie\Glue\MollieWebhookBackendApi\Dependency\Service\MollieWebhookBackendApiToUtilEncodingServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class WebhookProcessor implements WebhookProcessorInterface
{
    /**
     * @param \Mollie\Glue\MollieWebhookBackendApi\Dependency\Client\MollieWebhookBackendApiToMollieClientInterface $mollieClient
     * @param \Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade\MollieWebhookBackendApiToMollieFacadeInterface $mollieFacade
     * @param \Mollie\Glue\MollieWebhookBackendApi\Dependency\Service\MollieWebhookBackendApiToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        protected MollieWebhookBackendApiToMollieClientInterface $mollieClient,
        protected MollieWebhookBackendApiToMollieFacadeInterface $mollieFacade,
        protected MollieWebhookBackendApiToUtilEncodingServiceInterface $utilEncodingService,
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
        $data = $this->utilEncodingService->decodeJson($content);

        if (!isset($data['id'])) {
            return $glueResponseTransfer
                ->setHttpStatus(Response::HTTP_OK)
                ->setContent('Missing payment ID');
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setBody($data);

        $molliePaymentApiResponseTransfer = $this->mollieClient->getPaymentByTransactionId($mollieApiRequestTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
            return $glueResponseTransfer
                ->setHttpStatus(Response::HTTP_OK)
                ->setContent($molliePaymentApiResponseTransfer->getMessage());
        }

        $orderCollectionRequestTransfer = $this->createOrderCollectionRequestTransfer($molliePaymentApiResponseTransfer);

        $this->mollieFacade->updateOrderCollection($orderCollectionRequestTransfer);

        return $glueResponseTransfer
            ->setHttpStatus(Response::HTTP_OK)
            ->setContent('OK');
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    protected function createOrderCollectionRequestTransfer(
        MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer,
    ): OrderCollectionRequestTransfer {
        $orderCollectionRequestTransfer = new OrderCollectionRequestTransfer();
        $molliePaymentTransfer = $molliePaymentApiResponseTransfer->getMolliePayment();
        $orderCollectionRequestTransfer
            ->setId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus());

        return $orderCollectionRequestTransfer;
    }
}

<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MolliePaymentWriter implements MolliePaymentWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
        protected MollieToUtilEncodingServiceInterface $utilEncodingService,
    ) {
    }

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function addMolliePaymentData(int $idSalesOrder, MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $metadata = $molliePaymentTransfer->getMetadata();
        $metadataJson = $this->utilEncodingService->encodeJson($metadata);
        $molliePaymentTransfer->setMetadataJson($metadataJson);

        $this->entityManager->addMolliePaymentData($idSalesOrder, $molliePaymentTransfer);
    }
}

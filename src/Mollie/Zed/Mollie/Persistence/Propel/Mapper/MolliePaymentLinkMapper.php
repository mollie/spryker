<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

class MolliePaymentLinkMapper implements MolliePaymentLinkMapperInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface $moneyFacade
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        protected MollieToMoneyFacadeInterface $moneyFacade,
        protected MollieToUtilEncodingServiceInterface $utilEncodingService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $spyMolliePaymentLinkEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink
     */
    public function mapMolliePaymentLinkTransferToEntity(
        MolliePaymentLinkTransfer $molliePaymentLinkTransfer,
        SpyMolliePaymentLink $spyMolliePaymentLinkEntity,
    ): SpyMolliePaymentLink {
        $value = (float)$molliePaymentLinkTransfer->getAmount()->getValue();
        $amount = $this->moneyFacade->convertDecimalToInteger($value);

        $spyMolliePaymentLinkEntity
            ->setId($molliePaymentLinkTransfer->getId())
            ->setFkSalesOrder($molliePaymentLinkTransfer->getFkSalesOrder())
            ->setDescription($molliePaymentLinkTransfer->getDescription())
            ->setType($molliePaymentLinkTransfer->getType())
            ->setSequenceType($molliePaymentLinkTransfer->getSequenceType())
            ->setCurrency($molliePaymentLinkTransfer->getAmount()->getCurrency())
            ->setAmount($amount)
            ->setStatus($molliePaymentLinkTransfer->getStatus())
            ->setExpiryDate($molliePaymentLinkTransfer->getExpiresAt())
            ->setRedirectUrl($molliePaymentLinkTransfer->getRedirectUrl())
            ->setIsReusable($molliePaymentLinkTransfer->getReusable())
            ->setMode($molliePaymentLinkTransfer->getMode())
            ->setProfileid($molliePaymentLinkTransfer->getProfileId());

        if ($molliePaymentLinkTransfer->getLinks()?->getPaymentLink()?->getHref()) {
            $spyMolliePaymentLinkEntity->setPaymentLinkUrl($molliePaymentLinkTransfer->getLinks()->getPaymentLink()->getHref());
        }

        if ($molliePaymentLinkTransfer->getAllowedMethods()) {
            $allowedMethods = $this->utilEncodingService->encodeJson($molliePaymentLinkTransfer->getAllowedMethods());
            $spyMolliePaymentLinkEntity->setPaymentMethods($allowedMethods);
        }

        return $spyMolliePaymentLinkEntity;
    }

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $spyMolliePaymentLinkEntity
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapMolliePaymentLinkEntityToTransfer(SpyMolliePaymentLink $spyMolliePaymentLinkEntity): MolliePaymentLinkTransfer
    {
        $paymentLinkTransfer = new MolliePaymentLinkTransfer();
        $paymentLinkTransfer->fromArray($spyMolliePaymentLinkEntity->toArray(), true);
        $paymentLinkTransfer->setExpiresAt($spyMolliePaymentLinkEntity->getExpiryDate()->format('Y-m-d H:i:s'));

        return $paymentLinkTransfer;
    }
}

<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

class MolliePaymentLinkMapper implements MolliePaymentLinkMapperInterface
{
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
        $spyMolliePaymentLinkEntity
            ->setId($molliePaymentLinkTransfer->getId())
            ->setFkSalesOrder($molliePaymentLinkTransfer->getFkSalesOrder())
            ->setDescription($molliePaymentLinkTransfer->getDescription())
            ->setType($molliePaymentLinkTransfer->getType())
            ->setSequenceType($molliePaymentLinkTransfer->getSequenceType())
            ->setCurrency($molliePaymentLinkTransfer->getCurrencyCode())
            ->setAmount($molliePaymentLinkTransfer->getAmountInCents())
            ->setMinimumAmount($molliePaymentLinkTransfer->getMinimumAmountInCents())
            ->setStatus($molliePaymentLinkTransfer->getStatus())
            ->setExpiryDate($molliePaymentLinkTransfer->getExpiresAt())
            ->setRedirectUrl($molliePaymentLinkTransfer->getRedirectUrl())
            ->setIsReusable($molliePaymentLinkTransfer->getReusable())
            ->setMode($molliePaymentLinkTransfer->getMode())
            ->setProfileid($molliePaymentLinkTransfer->getProfileId())
            ->setPaymentMethods($molliePaymentLinkTransfer->getAllowedMethodsJson());

        if ($molliePaymentLinkTransfer->getLinks()?->getPaymentLink()?->getHref()) {
            $spyMolliePaymentLinkEntity->setPaymentLinkUrl($molliePaymentLinkTransfer->getLinks()->getPaymentLink()->getHref());
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

<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

class MolliePaymentLinkMapper implements MolliePaymentLinkMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
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
        $mollieAmountTransfer = $molliePaymentLinkTransfer->getAmount();
        $mollieMinimumAmountTransfer = $molliePaymentLinkTransfer->getMinimumAmount();
        $amount = $this->convertMollieAmountTransferToInteger($mollieAmountTransfer);
        $minimumAmount = $this->convertMollieAmountTransferToInteger($mollieMinimumAmountTransfer);
        $currency = $this->getCurrencyFromPaymentLinkAmounts($mollieAmountTransfer, $mollieMinimumAmountTransfer);

        $spyMolliePaymentLinkEntity
            ->setId($molliePaymentLinkTransfer->getId())
            ->setFkSalesOrder($molliePaymentLinkTransfer->getFkSalesOrder())
            ->setDescription($molliePaymentLinkTransfer->getDescription())
            ->setType($molliePaymentLinkTransfer->getType())
            ->setSequenceType($molliePaymentLinkTransfer->getSequenceType())
            ->setCurrency($currency)
            ->setAmount($amount)
            ->setMinimumAmount($minimumAmount)
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
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     *
     * @return int|null
     */
    protected function convertMollieAmountTransferToInteger(?MollieAmountTransfer $mollieAmountTransfer): ?int
    {
        if ($mollieAmountTransfer === null) {
            return null;
        }

        $value = (float)$mollieAmountTransfer->getValue();
        $amount = $this->mollieService->convertDecimalToInteger($value);

        return $amount;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieMinimumAmountTransfer
     *
     * @return string|null
     */
    protected function getCurrencyFromPaymentLinkAmounts(
        ?MollieAmountTransfer $mollieAmountTransfer,
        ?MollieAmountTransfer $mollieMinimumAmountTransfer,
    ): ?string {
        if ($mollieAmountTransfer !== null) {
            return $mollieAmountTransfer->getCurrency();
        }

        if ($mollieMinimumAmountTransfer !== null) {
            return $mollieMinimumAmountTransfer->getCurrency();
        }

        return null;
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

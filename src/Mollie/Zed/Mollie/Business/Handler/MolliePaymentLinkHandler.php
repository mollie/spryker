<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Handler;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentLinkHandler implements MolliePaymentLinkHandlerInterface
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface $moneyFacade
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieEntityManagerInterface $mollieEntityManager,
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieToMoneyFacadeInterface $moneyFacade,
        protected MollieToUtilEncodingServiceInterface $utilEncodingService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer
    {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($molliePaymentLinkTransfer);

        $molliePaymentLinkApiResponseTransfer = $this->mollieClient->createPaymentLink($mollieApiRequestTransfer);

        if ($molliePaymentLinkApiResponseTransfer->getIsSuccessful()) {
            $createdMolliePaymentLinkTransfer = $molliePaymentLinkApiResponseTransfer->getMolliePaymentLink();
            $createdMolliePaymentLinkTransfer->setFkSalesOrder($molliePaymentLinkTransfer->getFkSalesOrder());
            $createdMolliePaymentLinkTransfer = $this->addStorageValuesToMolliePaymentLink($createdMolliePaymentLinkTransfer);
            $this->mollieEntityManager->writePaymentLink($createdMolliePaymentLinkTransfer);
        }

        return $molliePaymentLinkApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    protected function addStorageValuesToMolliePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer
    {
        $mollieAmountTransfer = $molliePaymentLinkTransfer->getAmount();
        $mollieMinimumAmountTransfer = $molliePaymentLinkTransfer->getMinimumAmount();
        $allowedMethods = $molliePaymentLinkTransfer->getAllowedMethods();

        $amountInCents = $this->convertMollieAmountToCents($mollieAmountTransfer);
        $minimumAmountInCents = $this->convertMollieAmountToCents($mollieMinimumAmountTransfer);
        $currencyCode = $this->getCurrencyCodeFromPaymentLinkAmounts($mollieAmountTransfer, $mollieMinimumAmountTransfer);
        $allowedMethodsJson = $this->encodeAllowedMethodsToJson($allowedMethods);

        $molliePaymentLinkTransfer
            ->setAmountInCents($amountInCents)
            ->setMinimumAmountInCents($minimumAmountInCents)
            ->setCurrencyCode($currencyCode)
            ->setAllowedMethodsJson($allowedMethodsJson);

        return $molliePaymentLinkTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     *
     * @return int|null
     */
    protected function convertMollieAmountToCents(?MollieAmountTransfer $mollieAmountTransfer): ?int
    {
        if ($mollieAmountTransfer === null) {
            return null;
        }

        $decimalAmount = (float)$mollieAmountTransfer->getValue();
        $amountInCents = $this->moneyFacade->convertDecimalToInteger($decimalAmount);

        return $amountInCents;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieMinimumAmountTransfer
     *
     * @return string|null
     */
    protected function getCurrencyCodeFromPaymentLinkAmounts(
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
     * @param array<string> $allowedMethods
     *
     * @return string|null
     */
    protected function encodeAllowedMethodsToJson(array $allowedMethods): ?string
    {
        if ($allowedMethods === []) {
            return null;
        }

        $allowedMethodsJson = $this->utilEncodingService->encodeJson($allowedMethods);

        return $allowedMethodsJson;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkCreationSuccessful(int $idSalesOrder): bool
    {
        $paymentLinkTransfer = $this->mollieRepository->getPaymentLinkByFkSalesOrder($idSalesOrder);
        if (!$paymentLinkTransfer) {
            return false;
        }

        return true;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkStatusPaid(int $idSalesOrder): bool
    {
        $paymentLinkTransfer = $this->mollieRepository->getPaymentLinkByFkSalesOrder($idSalesOrder);
        if (!$paymentLinkTransfer) {
            return false;
        }

        return $paymentLinkTransfer->getStatus() === MollieConstants::STATUS_PAID;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkStatusExpired(int $idSalesOrder): bool
    {
        $paymentLinkTransfer = $this->mollieRepository->getPaymentLinkByFkSalesOrder($idSalesOrder);
        if (!$paymentLinkTransfer) {
            return false;
        }

        $expiryDateTime = $paymentLinkTransfer->getExpiresAt();
        $timezone = new DateTimeZone(date_default_timezone_get());
        $expiryDate = new DateTime($expiryDateTime, $timezone);
        $now = new DateTime('now', $timezone);

        return $now > $expiryDate;
    }
}

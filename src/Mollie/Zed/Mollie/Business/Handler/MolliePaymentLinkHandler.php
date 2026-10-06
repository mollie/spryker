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

            $mollieAmountTransfer = $createdMolliePaymentLinkTransfer->getAmount();
            $mollieMinimumAmountTransfer = $createdMolliePaymentLinkTransfer->getMinimumAmount();
            $allowedMethods = $createdMolliePaymentLinkTransfer->getAllowedMethods();

            $integerAmount = $this->convertMollieAmountToInteger($mollieAmountTransfer);
            $integerMinimumAmount = $this->convertMollieAmountToInteger($mollieMinimumAmountTransfer);
            $currencyCode = $this->getCurrencyCodeFromPaymentLinkAmounts($mollieAmountTransfer, $mollieMinimumAmountTransfer);
            $paymentMethods = $this->encodeAllowedMethods($allowedMethods);

            $this->mollieEntityManager->writePaymentLink(
                $createdMolliePaymentLinkTransfer,
                $integerAmount,
                $integerMinimumAmount,
                $currencyCode,
                $paymentMethods,
            );
        }

        return $molliePaymentLinkApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     *
     * @return int|null
     */
    protected function convertMollieAmountToInteger(?MollieAmountTransfer $mollieAmountTransfer): ?int
    {
        if ($mollieAmountTransfer === null) {
            return null;
        }

        $decimalAmount = (float)$mollieAmountTransfer->getValue();
        $integerAmount = $this->moneyFacade->convertDecimalToInteger($decimalAmount);

        return $integerAmount;
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
    protected function encodeAllowedMethods(array $allowedMethods): ?string
    {
        if ($allowedMethods === []) {
            return null;
        }

        $paymentMethods = $this->utilEncodingService->encodeJson($allowedMethods);

        return $paymentMethods;
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

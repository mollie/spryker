<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Filter;

use ArrayObject;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;

class MolliePaymentMethodsFilter implements MolliePaymentMethodsFilterInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Zed\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieServiceInterface $mollieService,
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieConfig $mollieConfig,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function applyFilter(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer
    {
        $requestTransfer = $this->createRequestTransfer($quoteTransfer);
        $molliePaymentMethodsApiResponseTransfer = $this->mollieClient->getEnabledPaymentMethods($requestTransfer);
        $molliePaymentMethods = $molliePaymentMethodsApiResponseTransfer->getCollection()->getMethods();

        $this->addIncludeWalletLogs($requestTransfer);

        $paymentMethodsTransfer = $this->filterMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer, $molliePaymentMethods);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createRequestTransfer(QuoteTransfer $quoteTransfer): MollieApiRequestTransfer
    {
        $grandTotal = $quoteTransfer->getTotals()->getGrandTotal();
        $currencyCode = $quoteTransfer->getCurrency()?->getCode();
        $mollieAmount = $this->mollieService->convertIntegerToMollieAmount($grandTotal, $currencyCode);

        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                (new MolliePaymentMethodQueryParametersTransfer())
                    ->setLocale($this->localeFacade->getCurrentLocale()->getLocaleName())
                    ->setBillingCountry($quoteTransfer->getBillingAddress()->getIso2Code())
                    ->setIncludeIssuers(true)
                    ->setIncludeWallets($this->mollieConfig->getMollieIncludeWallets())
                    ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF)
                    ->setAmount($mollieAmount),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $requestTransfer
     *
     * @return void
     */
    protected function addIncludeWalletLogs(MollieApiRequestTransfer $requestTransfer): void
    {
        $includeWallets = $requestTransfer->getMolliePaymentMethodQueryParameters()->getIncludeWallets() ?? [];
        $hasApplePay = in_array(MollieConfig::MOLLIE_WALLET_APPLE_PAY, $includeWallets, true);

        if ($hasApplePay) {
            return;
        }

        $this->getLogger()->info('Mollie Apple Pay not included in includeWallets.', [
            'includeWallets' => $includeWallets,
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\MolliePaymentMethodTransfer> $molliePaymentMethods
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function filterMolliePaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer,
        ArrayObject $molliePaymentMethods,
    ): PaymentMethodsTransfer {
        $activeMollieMethods = $this->indexMollieMethods($molliePaymentMethods);
        $indexedMolliePaymentConfigMethods = $this->getIndexedMolliePaymentConfigMethods($quoteTransfer);
        $grandTotal = $this->mollieService->convertIntegerToDecimal($quoteTransfer->getTotals()->getGrandTotal());

        $filteredMethods = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            $provider = $paymentMethodTransfer->getPaymentProvider();
            if (!$provider || !$this->isMollieProvider($provider->getPaymentProviderKey())) {
                $filteredMethods->append($paymentMethodTransfer);

                continue;
            }

            $mollieMethodId = $this->mollieConfig->getMolliePaymentMethod($paymentMethodTransfer->getPaymentMethodKey());

            if (!isset($activeMollieMethods[$mollieMethodId])) {
                continue;
            }

            $molliePaymentMethod = $activeMollieMethods[$mollieMethodId];
            $configMethod = $indexedMolliePaymentConfigMethods[$mollieMethodId] ?? null;

            if ($configMethod !== null && !$configMethod->getIsActive()) {
                continue;
            }

            $minimumAmount = $configMethod?->getMinimumAmount() ?? $molliePaymentMethod->getMinimumAmount();
            $maximumAmount = $configMethod?->getMaximumAmount() ?? $molliePaymentMethod->getMaximumAmount();

            if (!$this->isGrandTotalWithinValidMinAndMaxAmount($grandTotal, $minimumAmount, $maximumAmount)) {
                continue;
            }

            $filteredMethods->append($paymentMethodTransfer);
        }

        $paymentMethodsTransfer->setMethods($filteredMethods);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\MolliePaymentMethodTransfer> $molliePaymentMethods
     *
     * @return array<string, \Generated\Shared\Transfer\MolliePaymentMethodTransfer>
     */
    protected function indexMollieMethods(ArrayObject $molliePaymentMethods): array
    {
        $indexedMethods = [];

        foreach ($molliePaymentMethods as $method) {
            $indexedMethods[$method->getId()] = $method;
        }

        return $indexedMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer>
     */
    protected function getIndexedMolliePaymentConfigMethods(QuoteTransfer $quoteTransfer): array
    {
        $molliePaymentMethodConfigCriteriaTransfer = new MolliePaymentMethodConfigCriteriaTransfer();
        $molliePaymentMethodConfigCriteriaTransfer->setCurrencyCode($quoteTransfer->getCurrency()->getCode());

        $molliePaymentMethodConfigCollectionTransfer = $this->mollieRepository
            ->getPaymentMethodConfigCollection($molliePaymentMethodConfigCriteriaTransfer);

        $indexedPaymentConfigMethods = [];

        foreach ($molliePaymentMethodConfigCollectionTransfer->getConfigs() as $molliePaymentMethodConfigTransfer) {
            $indexedPaymentConfigMethods[$molliePaymentMethodConfigTransfer->getMollieId()] = $molliePaymentMethodConfigTransfer;
        }

        return $indexedPaymentConfigMethods;
    }

    /**
     * @param string $providerKey
     *
     * @return bool
     */
    protected function isMollieProvider(string $providerKey): bool
    {
        return str_starts_with(strtolower($providerKey), MollieConfig::MOLLIE_PAYMENT_PROVIDER);
    }

    /**
     * @param float $grandTotal
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $minimumAmount
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $maximumAmount
     *
     * @return bool
     */
    protected function isGrandTotalWithinValidMinAndMaxAmount(
        float $grandTotal,
        ?MollieAmountTransfer $minimumAmount,
        ?MollieAmountTransfer $maximumAmount,
    ): bool {
        $minimumAmount = $minimumAmount?->getValue();
        $maximumAmount = $maximumAmount?->getValue();

        if ($minimumAmount !== null && $grandTotal < (float)$minimumAmount) {
            return false;
        }

        if ($maximumAmount !== null && $grandTotal > (float)$maximumAmount) {
            return false;
        }

        return true;
    }
}

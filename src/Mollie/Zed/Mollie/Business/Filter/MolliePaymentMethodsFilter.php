<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Filter;

use ArrayObject;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\Reader\MolliePaymentMethodsConfigReaderInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Spryker\Shared\Log\LoggerTrait;

class MolliePaymentMethodsFilter implements MolliePaymentMethodsFilterInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     * @param \Mollie\Zed\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Zed\Mollie\Business\Reader\MolliePaymentMethodsConfigReaderInterface $molliePaymentMethodsConfigReader
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieServiceInterface $mollieService,
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieConfig $mollieConfig,
        protected MolliePaymentMethodsConfigReaderInterface $molliePaymentMethodsConfigReader,
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
        $mollieAmount = new MollieAmountTransfer();
        $mollieAmount->setCurrency($quoteTransfer->getCurrency()?->getCode());

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
        $indexedMolliePaymentConfigMethods = $this->getIndexMolliePaymentConfigMethods();
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

            $minimumAmount = $configMethod
                ? $configMethod->getMinAmount()
                : $molliePaymentMethod->getMinimumAmount();

            $maximumAmount = $configMethod
                ? $configMethod->getMaxAmount()
                : $molliePaymentMethod->getMaximumAmount();

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
     * @return array<string, \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer>
     */
    protected function getIndexMolliePaymentConfigMethods(): array
    {
        $molliePaymentMethodConfigCollectionTransfer = $this->molliePaymentMethodsConfigReader->getPaymentMethodConfigCollection(46);
        $indexedPaymentConfigMethods = [];

        foreach ($molliePaymentMethodConfigCollectionTransfer->getConfigs() as $molliePaymentMethodConfigTransfer) {
            $indexedPaymentConfigMethods[$molliePaymentMethodConfigTransfer->getPaymentMethodKey()] = $molliePaymentMethodConfigTransfer;
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
     * @param array|null $minimumAmount
     * @param array|null $maximumAmount
     *
     * @return bool
     */
    protected function isGrandTotalWithinValidMinAndMaxAmount(float $grandTotal, ?array $minimumAmount, ?array $maximumAmount): bool
    {
        $minimumAmount = isset($minimumAmount[MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE])
            ? (float)$minimumAmount[MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE]
            : null;

        $maximumAmount = isset($maximumAmount[MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE])
            ? (float)$maximumAmount[MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE]
            : null;

        if ($minimumAmount !== null && $grandTotal < $minimumAmount) {
            return false;
        }

        if ($maximumAmount !== null && $grandTotal > $maximumAmount) {
            return false;
        }

        return true;
    }
}

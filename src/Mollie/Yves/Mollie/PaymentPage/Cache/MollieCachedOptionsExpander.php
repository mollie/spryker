<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Cache;

use Generated\Shared\Transfer\MollieCacheOptionsTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Yves\Mollie\Mapper\MollieMapperInterface;
use Mollie\Yves\Mollie\MollieConfig;

class MollieCachedOptionsExpander implements MollieCachedOptionsExpanderInterface
{
    protected const array MOLLIE_LOGO_MAPPING = [
        'mollieKlarnaPayment' => 'klarnapaylater',
        'mollieKlarnaPayLaterPayment' => 'klarnapaylater',
        'mollieKlarnaPayNowPayment' => 'klarnapaynow',
        'mollieKlarnaSliceItPayment' => 'klarnasliceit',
    ];

    /**
     * @var array<string, string>
     */
    protected static array $mappedPaymentToLogo = [];

     /**
      * @var array<string, \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer>
      */
    protected static ?array $indexedPaymentMethodConfigCollection = null;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Yves\Mollie\Mapper\MollieMapperInterface $mapper
     * @param \Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface $localeClient
     * @param \Mollie\Yves\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieMapperInterface $mapper,
        protected MollieToLocaleClientInterface $localeClient,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param string $paymentMethod
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function expandOptions(string $paymentMethod, QuoteTransfer $quoteTransfer, array $options): array
    {
        $omsToPaymentMethodMapping = $this->config->getMollieOmsToPaymentMethodMapping();
        $indexedPaymentMethodConfigCollection = $this->getMolliePaymentMethodConfigCollection($quoteTransfer);

        $omsToPaymentMethodMapping = array_merge($omsToPaymentMethodMapping, static::MOLLIE_LOGO_MAPPING);
        $key = $omsToPaymentMethodMapping[$paymentMethod];
        $logoUniqueKey = $paymentMethod . MollieConstants::LOGO_URL;

        if (isset(self::$indexedPaymentMethodConfigCollection[$key])) {
            $options[$logoUniqueKey] = self::$indexedPaymentMethodConfigCollection[$key]->getImage();

            return $options;
        }

        if (isset(self::$mappedPaymentToLogo[$key])) {
            $options[$logoUniqueKey] = self::$mappedPaymentToLogo[$key];

            return $options;
        }

        $mollieCacheOptionsTransfer = $this->mapper->createMollieCacheOptionsTransfer($quoteTransfer);

        $responseTransfer = $this->getMollieResponse($mollieCacheOptionsTransfer);

        $methods = $responseTransfer->getCollection()->getMethods()->getArrayCopy();
        $mappedMethods = $this->mapMethodNamesToLogo($methods);

        self::$mappedPaymentToLogo = $mappedMethods;

        $omsToPaymentMethodMapping = $this->config->getMollieOmsToPaymentMethodMapping();
        $key = $omsToPaymentMethodMapping[$paymentMethod];

        $options[$logoUniqueKey] = $this->getLogoUrl($indexedPaymentMethodConfigCollection, self::$mappedPaymentToLogo, $key);

        return $options;
    }

    /**
     * @param array<string, \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer> $indexedPaymentMethodConfigCollection
     * @param array<string, string> $paymentMethodToLogoMap
     * @param string $methodKeyIdentifier
     *
     * @return string|null
     */
    protected function getLogoUrl(
        array $indexedPaymentMethodConfigCollection,
        array $paymentMethodToLogoMap,
        string $methodKeyIdentifier,
    ): ?string {
        if (isset($indexedPaymentMethodConfigCollection[$methodKeyIdentifier])) {
            return $indexedPaymentMethodConfigCollection[$methodKeyIdentifier]->getImage();
        }

        if (isset($paymentMethodToLogoMap[$methodKeyIdentifier])) {
            return $paymentMethodToLogoMap[$methodKeyIdentifier];
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCacheOptionsTransfer $mollieCacheOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function getMollieResponse(MollieCacheOptionsTransfer $mollieCacheOptionsTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        $mollieApiRequestTransfer = $this->mapper->createMollieApiRequestTransfer($mollieCacheOptionsTransfer);
        if ($this->config->isTestMode()) {
            return $this->mollieClient->getAllPaymentMethods($mollieApiRequestTransfer);
        }

        return $this->mollieClient->getEnabledPaymentMethods($mollieApiRequestTransfer);
    }

    /**
     * @param array<\Generated\Shared\Transfer\MolliePaymentMethodTransfer> $methods
     *
     * @return array<string, string>
     */
    protected function mapMethodNamesToLogo(array $methods): array
    {
        $mappedMethods = [];
        foreach ($methods as $transfer) {
            $image =
                $transfer->getImage()['size2x'] ??
                $transfer->getImage()['size1x'] ??
                $transfer->getImage()['svg'] ?? '';
             $mappedMethods[$transfer->getId()] = $image;
        }

        return $mappedMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer>
     */
    protected function getMolliePaymentMethodConfigCollection(QuoteTransfer $quoteTransfer): array
    {
        if (static::$indexedPaymentMethodConfigCollection !== null) {
            return static::$indexedPaymentMethodConfigCollection;
        }

        $molliePaymentMethodConfigCriteriaTransfer = new MolliePaymentMethodConfigCriteriaTransfer();
        $molliePaymentMethodConfigCriteriaTransfer->setCurrencyCode($quoteTransfer->getCurrency()->getCode());
        $molliePaymentConfigCollectionTransfer = $this->mollieClient
            ->getPaymentMethodConfigCollection($molliePaymentMethodConfigCriteriaTransfer);

        $indexedPaymentMethodConfigCollection = [];

        foreach ($molliePaymentConfigCollectionTransfer->getConfigs() as $molliePaymentMethodConfigTransfer) {
            $indexedPaymentMethodConfigCollection[$molliePaymentMethodConfigTransfer->getMollieId()] = $molliePaymentMethodConfigTransfer;
        }

        static::$indexedPaymentMethodConfigCollection = $indexedPaymentMethodConfigCollection;

        return static::$indexedPaymentMethodConfigCollection;
    }
}

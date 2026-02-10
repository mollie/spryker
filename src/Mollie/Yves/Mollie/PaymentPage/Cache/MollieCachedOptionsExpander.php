<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Cache;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Yves\Mollie\Mapper\MollieMapperInterface;
use Mollie\Yves\Mollie\MollieConfig;

class MollieCachedOptionsExpander implements MollieCachedOptionsExpanderInterface
{
    /**
     * @var array<string, string>
     */
    protected static array $mappedPaymentToLogo = [];

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
        $key = $omsToPaymentMethodMapping[$paymentMethod];
        $logoUniqueKey = $paymentMethod . MollieConstants::LOGO_URL;

        if (isset(self::$mappedPaymentToLogo[$key])) {
            $options[$logoUniqueKey] = self::$mappedPaymentToLogo[$key];

            return $options;
        }

        $locale = $this->localeClient->getCurrentLocale();
        $billingCountry = $quoteTransfer->getBillingAddress()->getIso2Code();
        $mollieApiRequestTransfer = $this->mapper->createMollieApiRequestTransfer($locale, $billingCountry);

        $responseTransfer = $this->mollieClient->getEnabledPaymentMethods($mollieApiRequestTransfer);
        $methods = $responseTransfer->getCollection()->getMethods()->getArrayCopy();
        $mappedMethods = $this->mapMethodNamesToLogo($methods);

        self::$mappedPaymentToLogo = $mappedMethods;

        $omsToPaymentMethodMapping = $this->config->getMollieOmsToPaymentMethodMapping();
        $key = $omsToPaymentMethodMapping[$paymentMethod];

        if (isset($mappedMethods[$key])) {
            $options[$logoUniqueKey] = $mappedMethods[$key];

            return $options;
        }

        return $options;
    }

    /**
     * @param array<\Generated\Shared\Transfer\MolliePaymentMethodTransfer> $methods
     *
     * @return array<string, string>
     */
    public function mapMethodNamesToLogo(array $methods): array
    {
        $mappedMethods = [];
        foreach ($methods as $transfer) {
            $image =
                $transfer->getImage()['size2x'] ??
                $transfer->getImage()['size1x'] ??
                $transfer->getImage()['svg'] ?? '';
             $mappedMethods[$transfer->getId()] = $image;
//            $mappedMethods['creditcard'] = $transfer->getImage()['svg'];
//            $mappedMethods[$transfer->getDescription()] = $transfer->getImage()['size2x'];
        }

        return $mappedMethods;
    }
}

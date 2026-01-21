<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

class MolliePaymentMethodsDataProvider
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        private MollieClientInterface $mollieClient,
        protected MollieToLocaleFacadeInterface $localeFacade,
    ) {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getData(Request $request): MolliePaymentMethodsApiResponseTransfer
    {
        $showOnlyEnabled = $request->query->get(MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED);
        $requestTransfer = $this->createRequestTransfer();
        if ($showOnlyEnabled) {
            return $this->mollieClient->getEnabledPaymentMethods($requestTransfer);
        }

        return $this->mollieClient->getAllPaymentMethods($requestTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createRequestTransfer(): MollieApiRequestTransfer
    {
        $currentLocale = $this->localeFacade->getCurrentLocale();

        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                (new MolliePaymentMethodQueryParametersTransfer())
                    ->setSequenceType('oneoff')
                    ->setLocale($currentLocale->getLocaleName()),
            );
    }
}

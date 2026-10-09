<?php


declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
use Ramsey\Uuid\Uuid;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class ExpressCheckoutController extends AbstractController
{
    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_SESSION_DESCRIPTION = 'Express Checkout Session';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function resolveEnabledMethodsAction(Request $request): JsonResponse
    {
        $mollieExpressCheckoutConfigCollectionTransfer = $this->getClient()->getExpressCheckoutConfigCollection(
            new MollieExpressCheckoutConfigCriteriaTransfer(),
        );

        $expressMethods = $this->mapExpressMethodsToIsEnabled($mollieExpressCheckoutConfigCollectionTransfer);

        return new JsonResponse(['expressMethods' => $expressMethods]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createSessionAction(Request $request): JsonResponse
    {
        $mollieExpressCheckoutSessionApiResponseTransfer = $this->createExpressCheckoutSession($request);

        if (!$mollieExpressCheckoutSessionApiResponseTransfer->getIsSuccessful()) {
            return new JsonResponse(
                ['message' => $mollieExpressCheckoutSessionApiResponseTransfer->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        $mollieExpressCheckoutSessionTransfer = $mollieExpressCheckoutSessionApiResponseTransfer->getExpressCheckoutSession();
        $this->storeExpressCheckoutSession($request, $mollieExpressCheckoutSessionTransfer->getId());

        return new JsonResponse([
            'clientAccessToken' => $mollieExpressCheckoutSessionTransfer->getClientAccessToken(),
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return array<string, bool>
     */
    protected function mapExpressMethodsToIsEnabled(
        MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer,
    ): array {
        $expressMethods = [];
        foreach ($mollieExpressCheckoutConfigCollectionTransfer->getConfigs() as $mollieExpressCheckoutConfigTransfer) {
            $expressMethods[$mollieExpressCheckoutConfigTransfer->getMethod()] = $mollieExpressCheckoutConfigTransfer->getIsEnabled();
        }

        return $expressMethods;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer
     */
    protected function createExpressCheckoutSession(Request $request): MollieExpressCheckoutSessionApiResponseTransfer
    {
        $quoteClient = $this->getFactory()->getQuoteClient();
        $quoteTransfer = $quoteClient->getQuote();

        $uuid = Uuid::uuid4();
        $expressCheckoutUuid = $uuid->toString();

        $request->getSession()->set($this->getFactory()->getConfig()->getExpressCheckoutUuidSessionKey(), $expressCheckoutUuid);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setDescription(static::EXPRESS_CHECKOUT_SESSION_DESCRIPTION)
            ->setExpressCheckoutUuid($expressCheckoutUuid)
            ->setRedirectUrl(
                $request->getSchemeAndHttpHost() . MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_REDIRECT,
            );

        $mollieExpressCheckoutShippingOptionsResponseTransfer = $this->getClient()->getExpressCheckoutShippingOptions(
            (new MollieExpressCheckoutShippingOptionsRequestTransfer())
                ->setQuote(clone $quoteTransfer)
                ->setShippingAddress($this->createShippingOptionsAddress($quoteTransfer)),
        );

        if (!$mollieExpressCheckoutShippingOptionsResponseTransfer->getIsSuccessful()) {
            return (new MollieExpressCheckoutSessionApiResponseTransfer())
                ->setIsSuccessful(false)
                ->setMessage($mollieExpressCheckoutShippingOptionsResponseTransfer->getError());
        }

        $mollieApiRequestTransfer->setShippingOptions($mollieExpressCheckoutShippingOptionsResponseTransfer->getOptions());

        return $this->getClient()->createExpressCheckoutSession($mollieApiRequestTransfer);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $mollieSessionId
     *
     * @return void
     */
    protected function storeExpressCheckoutSession(Request $request, string $mollieSessionId): void
    {
        $config = $this->getFactory()->getConfig();

        $request->getSession()->set($config->getExpressCheckoutSessionIdSessionKey(), $mollieSessionId);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createShippingOptionsAddress(QuoteTransfer $quoteTransfer): AddressTransfer
    {
        $customerShippingAddresses = $quoteTransfer->getCustomer()?->getShippingAddress();

        if ($customerShippingAddresses && $customerShippingAddresses->count() > 0) {
            return clone $customerShippingAddresses->offsetGet(0);
        }

        $storeCountries = $quoteTransfer->getStore()?->getCountries() ?? [];

        return (new AddressTransfer())->setIso2Code($storeCountries[0] ?? null);
    }
}

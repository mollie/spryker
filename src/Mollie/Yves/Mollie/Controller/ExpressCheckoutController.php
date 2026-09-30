<?php


declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
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
        $request->getSession()->set(
            $this->getFactory()->getConfig()->getExpressCheckoutSessionIdSessionKey(),
            $mollieExpressCheckoutSessionTransfer->getId(),
        );

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
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        // Our own reference on the session metadata, used to match the payment Mollie creates (webhook) to this checkout.
        // probaj maknuti to
        $expressCheckoutReference = bin2hex(random_bytes(16));
        $request->getSession()->set(
            $this->getFactory()->getConfig()->getExpressCheckoutReferenceSessionKey(),
            $expressCheckoutReference,
        );

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setExpressCheckoutReference($expressCheckoutReference)
            ->setQuote($quoteTransfer)
            ->setDescription(static::EXPRESS_CHECKOUT_SESSION_DESCRIPTION)
            ->setRedirectUrl(
                $request->getSchemeAndHttpHost() . MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_REDIRECT,
            );

        return $this->getClient()->createExpressCheckoutSession($mollieApiRequestTransfer);
    }
}

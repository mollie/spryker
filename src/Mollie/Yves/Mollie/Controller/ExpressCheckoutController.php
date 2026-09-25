<?php


declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
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
     * @var string
     */
    protected const EXPRESS_CHECKOUT_REDIRECT_URL_PLACEHOLDER = 'https://example.org/checkout/express-redirect';

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
        $mollieExpressCheckoutSessionApiResponseTransfer = $this->createExpressCheckoutSession();

        if (!$mollieExpressCheckoutSessionApiResponseTransfer->getIsSuccessful()) {
            return new JsonResponse(
                ['message' => $mollieExpressCheckoutSessionApiResponseTransfer->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse([
            'clientAccessToken' => $mollieExpressCheckoutSessionApiResponseTransfer->getExpressCheckoutSession()->getClientAccessToken(),
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
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer
     */
    protected function createExpressCheckoutSession(): MollieExpressCheckoutSessionApiResponseTransfer
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setDescription(static::EXPRESS_CHECKOUT_SESSION_DESCRIPTION)
            ->setRedirectUrl(static::EXPRESS_CHECKOUT_REDIRECT_URL_PLACEHOLDER);

        return $this->getClient()->createExpressCheckoutSession($mollieApiRequestTransfer);
    }
}

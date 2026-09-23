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
     * Placeholder until the dedicated Express Checkout redirect/reconciliation controller exists (see
     * MOLLIE_EXPRESS_CHECKOUT_CONTEXT.md §8 step 7) — not decided/built yet.
     *
     * @var string
     */
    protected const EXPRESS_CHECKOUT_REDIRECT_URL_PLACEHOLDER = 'https://example.org/checkout/express-redirect';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function initAction(Request $request): JsonResponse
    {
        $mollieExpressCheckoutConfigCollectionTransfer = $this->getClient()->getExpressCheckoutConfigCollection(
            new MollieExpressCheckoutConfigCriteriaTransfer(),
        );

        $enabledMethods = $this->extractEnabledMethods($mollieExpressCheckoutConfigCollectionTransfer);

        if (!$enabledMethods) {
            return new JsonResponse(['enabledMethods' => []]);
        }

        $mollieExpressCheckoutSessionApiResponseTransfer = $this->createExpressCheckoutSession();

        if (!$mollieExpressCheckoutSessionApiResponseTransfer->getIsSuccessful()) {
            return new JsonResponse(
                ['message' => $mollieExpressCheckoutSessionApiResponseTransfer->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse([
            'enabledMethods' => $enabledMethods,
            'clientAccessToken' => $mollieExpressCheckoutSessionApiResponseTransfer->getExpressCheckoutSession()->getClientAccessToken(),
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return list<string>
     */
    protected function extractEnabledMethods(
        MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer,
    ): array {
        $enabledMethods = [];
        foreach ($mollieExpressCheckoutConfigCollectionTransfer->getConfigs() as $mollieExpressCheckoutConfigTransfer) {
            if ($mollieExpressCheckoutConfigTransfer->getIsEnabled()) {
                $enabledMethods[] = $mollieExpressCheckoutConfigTransfer->getMethod();
            }
        }

        return $enabledMethods;
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

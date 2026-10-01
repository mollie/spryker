<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class ExpressCheckoutOrderController extends AbstractMollieController
{
    /**
     * @var string
     */
    protected const RESPONSE_KEY_IS_SUCCESSFUL = 'isSuccessful';

    /**
     * @var string
     */
    protected const RESPONSE_KEY_ORDER_REFERENCE = 'orderReference';

    /**
     * @var string
     */
    protected const RESPONSE_KEY_ERRORS = 'errors';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ADDRESSES_MISSING = 'Please fill in the billing and shipping address to use express checkout.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SESSION_EXPIRED = 'The express checkout session expired. Please reload the cart and try again.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function placeOrderAction(Request $request): JsonResponse
    {
        $quoteClient = $this->getFactory()->getQuoteClient();
        $quoteTransfer = $quoteClient->getQuote();

        if (!$this->getFactory()->createExpressCheckoutAddressChecker()->hasAddresses($quoteTransfer)) {
            return $this->createFailedResponse([static::ERROR_MESSAGE_ADDRESSES_MISSING]);
        }

        $uuidSessionKey = $this->getFactory()->getConfig()->getExpressCheckoutUuidSessionKey();
        $expressCheckoutUuid = $request->getSession()->get($uuidSessionKey);

        if (!$expressCheckoutUuid) {
            return $this->createFailedResponse([static::ERROR_MESSAGE_SESSION_EXPIRED]);
        }

        $mollieExpressCheckoutOrderResponseTransfer = $this->getClient()->placeExpressCheckoutOrder(
            (new MollieExpressCheckoutOrderRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setExpressCheckoutUuid($expressCheckoutUuid),
        );

        if (!$mollieExpressCheckoutOrderResponseTransfer->getIsSuccessful()) {
            return $this->createFailedResponse($mollieExpressCheckoutOrderResponseTransfer->getErrors());
        }

        $orderReference = $mollieExpressCheckoutOrderResponseTransfer->getOrderReferenceOrFail();

        $quoteClient->setQuote(
            $mollieExpressCheckoutOrderResponseTransfer->getQuoteOrFail()
                ->setOrderReference($orderReference)
                ->setCheckoutConfirmed(true)
                ->setIsOrderPlacedSuccessfully(true),
        );
        $request->getSession()->remove($uuidSessionKey);

        return new JsonResponse([
            static::RESPONSE_KEY_IS_SUCCESSFUL => true,
            static::RESPONSE_KEY_ORDER_REFERENCE => $orderReference,
            static::RESPONSE_KEY_ERRORS => [],
        ]);
    }

    /**
     * @param array<string> $errors
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function createFailedResponse(array $errors): JsonResponse
    {
        return new JsonResponse([
            static::RESPONSE_KEY_IS_SUCCESSFUL => false,
            static::RESPONSE_KEY_ORDER_REFERENCE => null,
            static::RESPONSE_KEY_ERRORS => $errors,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}

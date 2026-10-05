<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class ExpressCheckoutShippingController extends AbstractMollieController
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const PAYLOAD_KEY_SESSION_ID = 'sessionId';

    /**
     * @var string
     */
    protected const PAYLOAD_KEY_SHIPPING_ADDRESS = 'shippingAddress';

    /**
     * @var string
     */
    protected const ERROR_TYPE = 'https://docs.mollie.com/reference/errors/unsupported-postal-code';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SESSION_UNKNOWN = 'This checkout has expired. Please reload the cart and try again.';

    /**
     * Mollie's shipping callback: called server-to-server (no shopper session) whenever the shopper picks or changes
     * the shipping address in the express sheet. Payload: sessionId + partial shippingAddress
     * (postalCode, city, region, country). Answers with the Spryker shipment methods for that address,
     * or 422 so the shopper sees an error and cannot pay. Must answer within 15 seconds.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function shippingOptionsAction(Request $request): JsonResponse
    {
        $payload = $this->getFactory()->getUtilEncodingService()->decodeJson($request->getContent()) ?? [];
        $this->getLogger()->info('Mollie express checkout shipping callback.', ['payload' => $payload]);

        $quoteTransfer = $this->findQuoteCopy($payload[static::PAYLOAD_KEY_SESSION_ID] ?? null);

        if (!$quoteTransfer) {
            return $this->createErrorResponse(static::ERROR_MESSAGE_SESSION_UNKNOWN);
        }

        $mollieExpressCheckoutShippingOptionsResponseTransfer = $this->getClient()->getExpressCheckoutShippingOptions(
            (new MollieExpressCheckoutShippingOptionsRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setShippingAddress($this->mapShippingAddress($payload[static::PAYLOAD_KEY_SHIPPING_ADDRESS] ?? [])),
        );

        if (!$mollieExpressCheckoutShippingOptionsResponseTransfer->getIsSuccessful()) {
            return $this->createErrorResponse((string)$mollieExpressCheckoutShippingOptionsResponseTransfer->getError());
        }

        return new JsonResponse(['options' => $this->mapOptions($mollieExpressCheckoutShippingOptionsResponseTransfer)]);
    }

    /**
     * @param mixed $mollieSessionId
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|null
     */
    protected function findQuoteCopy(mixed $mollieSessionId): ?QuoteTransfer
    {
        if (!is_string($mollieSessionId) || $mollieSessionId === '') {
            return null;
        }

        $quoteData = $this->getFactory()->getStorageClient()->get(
            $this->getFactory()->getConfig()->getExpressCheckoutQuoteStorageKey($mollieSessionId),
        );

        return is_array($quoteData) ? (new QuoteTransfer())->fromArray($quoteData, true) : null;
    }

    /**
     * @param array<string, mixed> $shippingAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function mapShippingAddress(array $shippingAddress): AddressTransfer
    {
        return (new AddressTransfer())
            ->setZipCode($shippingAddress['postalCode'] ?? null)
            ->setCity($shippingAddress['city'] ?? null)
            ->setRegion($shippingAddress['region'] ?? null)
            ->setIso2Code($shippingAddress['country'] ?? null);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer $mollieExpressCheckoutShippingOptionsResponseTransfer
     *
     * @return array<int, array<string, mixed>>
     */
    protected function mapOptions(
        MollieExpressCheckoutShippingOptionsResponseTransfer $mollieExpressCheckoutShippingOptionsResponseTransfer,
    ): array {
        $options = [];
        foreach ($mollieExpressCheckoutShippingOptionsResponseTransfer->getOptions() as $optionTransfer) {
            $options[] = [
                'reference' => $optionTransfer->getReference(),
                'description' => $optionTransfer->getDescription(),
                'amount' => [
                    'currency' => $optionTransfer->getAmountOrFail()->getCurrency(),
                    'value' => $optionTransfer->getAmountOrFail()->getValue(),
                ],
            ];
        }

        return $options;
    }

    /**
     * @param string $detail
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function createErrorResponse(string $detail): JsonResponse
    {
        return new JsonResponse([
            'type' => static::ERROR_TYPE,
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'detail' => $detail,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}

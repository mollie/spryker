<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class ApplePayController extends AbstractController
{
    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_VALIDATION_URL = 'validationUrl';

    /**
     * @var string
     */
    protected const RESPONSE_PARAMETER_MESSAGE = 'message';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_INVALID_VALIDATION_URL = 'Invalid Apple Pay validation URL.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createPaymentSessionAction(Request $request): JsonResponse
    {
        $requestBody = $request->toArray();
        $applePayValidationUrl = (string)($requestBody[static::REQUEST_PARAMETER_VALIDATION_URL] ?? '');
        $applePayDomain = $request->getHost();

        $applePayValidationUrlValidator = $this->getFactory()->createApplePayValidationUrlValidator();
        $isApplePayValidationUrlValid = $applePayValidationUrlValidator->isValid($applePayValidationUrl);
        if (!$isApplePayValidationUrlValid) {
            return new JsonResponse(
                [static::RESPONSE_PARAMETER_MESSAGE => static::ERROR_MESSAGE_INVALID_VALIDATION_URL],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer
            ->setApplePayDomain($applePayDomain)
            ->setApplePayValidationUrl($applePayValidationUrl);

        $mollieApplePayPaymentSessionApiResponseTransfer = $this->getClient()->createApplePayPaymentSession($mollieApiRequestTransfer);

        if (!$mollieApplePayPaymentSessionApiResponseTransfer->getIsSuccessful()) {
            return new JsonResponse(
                [static::RESPONSE_PARAMETER_MESSAGE => $mollieApplePayPaymentSessionApiResponseTransfer->getMessage()],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $applePayPaymentSession = $mollieApplePayPaymentSessionApiResponseTransfer->getApplePayPaymentSession();

        return new JsonResponse($applePayPaymentSession);
    }
}

<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Spryker\Shared\Log\LoggerTrait;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class WebhookController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function webhookAction(Request $request): Response
    {
        $response = new Response();

        $content = $request->getContent();
        $data = $this->getFactory()->getUtilEncodingService()->decodeJson($content);

        if (!isset($data['id'])) {
            return $response
                ->setStatusCode(Response::HTTP_OK)
                ->setContent('Missing payment ID');
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setTransactionId($data['id']);

        $molliePaymentApiResponseTransfer = $this->getFactory()->getMollieApiClient()->getPaymentByTransactionId($mollieApiRequestTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
            return $response
                ->setStatusCode(Response::HTTP_OK)
                ->setContent($molliePaymentApiResponseTransfer->getMessage());
        }

        $orderCollectionRequestTransfer = $this->createOrderCollectionRequestTransfer($molliePaymentApiResponseTransfer);

        $this->getClient()->updateOrderCollection($orderCollectionRequestTransfer);

        return $response
            ->setStatusCode(Response::HTTP_OK)
            ->setContent($molliePaymentApiResponseTransfer->getMessage());
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    protected function createOrderCollectionRequestTransfer(
        MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer,
    ): OrderCollectionRequestTransfer {
        $orderCollectionRequestTransfer = new OrderCollectionRequestTransfer();
        $molliePaymentTransfer = $molliePaymentApiResponseTransfer->getMolliePayment();
        $orderCollectionRequestTransfer
            ->setId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus());

        return $orderCollectionRequestTransfer;
    }
}

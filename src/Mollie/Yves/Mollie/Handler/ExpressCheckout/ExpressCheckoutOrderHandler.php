<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Mapper\ExpressCheckout\ExpressCheckoutOrderRequestMapperInterface;
use Spryker\Shared\Log\LoggerTrait;

class ExpressCheckoutOrderHandler implements ExpressCheckoutOrderHandlerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const LOG_MESSAGE_ORDER_NOT_CREATED = 'Express checkout order could not be created after payment.';

    /**
     * @param \Mollie\Yves\Mollie\Mapper\ExpressCheckout\ExpressCheckoutOrderRequestMapperInterface $expressCheckoutOrderRequestMapper
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface $quoteClient
     */
    public function __construct(
        protected ExpressCheckoutOrderRequestMapperInterface $expressCheckoutOrderRequestMapper,
        protected MollieClientInterface $mollieClient,
        protected MollieToQuoteClientInterface $quoteClient,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param string $expressCheckoutUuid
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeOrder(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        string $expressCheckoutUuid,
    ): MollieExpressCheckoutOrderResponseTransfer {
        $mollieExpressCheckoutOrderRequestTransfer = (new MollieExpressCheckoutOrderRequestTransfer())
            ->setQuote($this->quoteClient->getQuote())
            ->setExpressCheckoutUuid($expressCheckoutUuid);

        $mollieExpressCheckoutOrderRequestTransfer = $this->expressCheckoutOrderRequestMapper
            ->mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
                $mollieExpressCheckoutSessionTransfer,
                $mollieExpressCheckoutOrderRequestTransfer,
            );

        $mollieExpressCheckoutOrderResponseTransfer = $this->mollieClient->placeExpressCheckoutOrder($mollieExpressCheckoutOrderRequestTransfer);

        if (!$mollieExpressCheckoutOrderResponseTransfer->getIsSuccessful()) {
            $this->createExpressCheckoutPaymentRefund(
                $mollieExpressCheckoutSessionTransfer,
                $mollieExpressCheckoutOrderResponseTransfer,
                $expressCheckoutUuid,
            );

            return $mollieExpressCheckoutOrderResponseTransfer;
        }

        $this->updateQuote($mollieExpressCheckoutOrderResponseTransfer);

        return $mollieExpressCheckoutOrderResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer $mollieExpressCheckoutOrderResponseTransfer
     * @param string $expressCheckoutUuid
     *
     * @return void
     */
    protected function createExpressCheckoutPaymentRefund(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        MollieExpressCheckoutOrderResponseTransfer $mollieExpressCheckoutOrderResponseTransfer,
        string $expressCheckoutUuid,
    ): void {
        $mollieSessionId = $mollieExpressCheckoutSessionTransfer->getId();

        $this->getLogger()->critical(static::LOG_MESSAGE_ORDER_NOT_CREATED, [
            'mollieSessionId' => $mollieSessionId,
            'expressCheckoutUuid' => $expressCheckoutUuid,
            'errors' => $mollieExpressCheckoutOrderResponseTransfer->getErrors(),
        ]);

        $mollieExpressCheckoutPaymentRefundTransfer = (new MollieExpressCheckoutPaymentRefundTransfer())
            ->setExpressCheckoutUuid($expressCheckoutUuid)
            ->setMollieSessionId($mollieSessionId);

        $this->mollieClient->createExpressCheckoutPaymentRefund($mollieExpressCheckoutPaymentRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer $mollieExpressCheckoutOrderResponseTransfer
     *
     * @return void
     */
    protected function updateQuote(MollieExpressCheckoutOrderResponseTransfer $mollieExpressCheckoutOrderResponseTransfer): void
    {
        $orderReference = $mollieExpressCheckoutOrderResponseTransfer->getOrderReference();

        $placedQuoteTransfer = $mollieExpressCheckoutOrderResponseTransfer->getQuote()
            ->setOrderReference($orderReference)
            ->setCheckoutConfirmed(true)
            ->setIsOrderPlacedSuccessfully(true);

        $this->quoteClient->setQuote($placedQuoteTransfer);
    }
}

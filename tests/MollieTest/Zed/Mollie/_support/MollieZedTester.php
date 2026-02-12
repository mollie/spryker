<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie;

use ArrayObject;
use Codeception\Actor;
use DateTime;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;
use Orm\Zed\Mollie\Persistence\SpyRefundMollieQuery;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class MollieZedTester extends Actor
{
    use _generated\MollieZedTesterActions;

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollie
     */
    public function createMolliePayment(int $idSalesOrder): SpyPaymentMollie
    {
        $spyPaymentMollieEntity = (new SpyPaymentMollie())
            ->setFkSalesOrder($idSalesOrder)
            ->setTransactionId('test_123')
            ->setStatus('open')
            ->setDescription('Test payment')
            ->setSequenceType('123')
            ->setMetadata('[]')
            ->setCreatedAt(new DateTime())
            ->setExpiresAt(new DateTime('+1 year'));

        $spyPaymentMollieEntity->save();

        return $spyPaymentMollieEntity;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollie
     */
    public function findMolliePayment(int $idSalesOrder): SpyPaymentMollie
    {
        return SpyPaymentMollieQuery::create()
            ->filterByFkSalesOrder($idSalesOrder)
            ->findOne();
    }

    /**
     * @param \ArrayObject $orderItems
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function createMollieRefund(ArrayObject $orderItems): SpyRefundMollie
    {
        foreach ($orderItems as $orderItem) {
            $spyRefundMollieEntity = (new SpyRefundMollie())
                ->setFkSalesOrderItem($orderItem->getIdSalesOrderItem())
                ->setDescription('Test refund')
                ->setCurrency('EUR')
                ->setValue('22.22')
                ->setStatus('refunded')
                ->setMetadata('[]')
                ->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ')
                ->setRefundId('re_yuj7TaDpm877xZQzP8ULJ')
                ->setCreatedAt(new DateTime());

            $spyRefundMollieEntity->save();
        }

        return $spyRefundMollieEntity;
    }

    /**
     * @param string|int $refundId
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function findMollieRefund(string $refundId): SpyRefundMollie
    {
        return SpyRefundMollieQuery::create()
            ->filterByRefundId($refundId)
            ->findOne();
    }

    /**
     * @return string
     */
    public function getMollieRefunds(): string
    {
        return '{"refunds":[{"resource":"refund","id":"re_YBgXAPCDySfoay4KCpuLJ","mode":"test","amount":{"value":"102.90","currency":"EUR"},"status":"refunded","createdAt":"2026-02-09T11:55:23+00:00","description":"DE--119598-176413-5285","metadata":["{\"orderReference\":\"DE--119598-176413-5285\"}"],"paymentId":"tr_R3UACHQ7EiUdZwt9UouLJ","_links":{"self":{"href":"https://api.mollie.com/v2/payments/tr_R3UACHQ7EiUdZwt9UouLJ/refunds/re_YBgXAPCDySfoay4KCpuLJ","type":"application/hal+json"},"payment":{"href":"https://api.mollie.com/v2/payments/tr_R3UACHQ7EiUdZwt9UouLJ","type":"application/hal+json"},"documentation":{"href":"https://docs.mollie.com/reference/get-refund","type":"text/html"}}}]}';
    }
}

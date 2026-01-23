<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie;

use Codeception\Actor;
use DateTime;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;

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
}

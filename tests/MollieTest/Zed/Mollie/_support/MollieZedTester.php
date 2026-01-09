<?php


declare(strict_types = 1);

namespace MollieTest\Zed\Mollie;

use Codeception\Actor;
use DateTime;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;

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
     * @var string
     */
    public const TEST_STATE_MACHINE_NAME = 'Test01';

    /**
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    public function createOrder(): SaveOrderTransfer
    {
        return $this->haveOrder([], static::TEST_STATE_MACHINE_NAME);
    }

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
            ->setStatus('paid')
            ->setDescription('Test payment')
            ->setSequenceType('123')
            ->setMetadata('[]')
            ->setCreatedAt(new DateTime())
            ->setExpiresAt(new DateTime('+1 year'));

        $spyPaymentMollieEntity->save();

        return $spyPaymentMollieEntity;
    }
}

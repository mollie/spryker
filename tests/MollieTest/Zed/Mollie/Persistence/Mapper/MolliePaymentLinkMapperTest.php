<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Persistence\Mapper;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapperInterface;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

class MolliePaymentLinkMapperTest extends Unit
{
    /**
     * @var string
     */
    protected const CURRENCY_CODE = 'EUR';

    /**
     * @return void
     */
    public function testMapMolliePaymentLinkTransferToEntityCopiesAmountInCentsAndCurrency(): void
    {
        $molliePaymentLinkTransfer = $this->createMolliePaymentLinkTransfer()
            ->setAmountInCents(2550);

        $spyMolliePaymentLinkEntity = $this->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkTransferToEntity($molliePaymentLinkTransfer, new SpyMolliePaymentLink());

        $this->assertSame(2550, $spyMolliePaymentLinkEntity->getAmount());
        $this->assertNull($spyMolliePaymentLinkEntity->getMinimumAmount());
        $this->assertSame(static::CURRENCY_CODE, $spyMolliePaymentLinkEntity->getCurrency());
    }

    /**
     * @return void
     */
    public function testMapMolliePaymentLinkTransferToEntityCopiesMinimumAmountInCents(): void
    {
        $molliePaymentLinkTransfer = $this->createMolliePaymentLinkTransfer()
            ->setMinimumAmountInCents(1000);

        $spyMolliePaymentLinkEntity = $this->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkTransferToEntity($molliePaymentLinkTransfer, new SpyMolliePaymentLink());

        $this->assertNull($spyMolliePaymentLinkEntity->getAmount());
        $this->assertSame(1000, $spyMolliePaymentLinkEntity->getMinimumAmount());
        $this->assertSame(static::CURRENCY_CODE, $spyMolliePaymentLinkEntity->getCurrency());
    }

    /**
     * @return void
     */
    public function testMapMolliePaymentLinkTransferToEntityCopiesAllowedMethodsJson(): void
    {
        $molliePaymentLinkTransfer = $this->createMolliePaymentLinkTransfer()
            ->setAmountInCents(2550)
            ->setAllowedMethodsJson('["ideal","creditcard"]');

        $spyMolliePaymentLinkEntity = $this->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkTransferToEntity($molliePaymentLinkTransfer, new SpyMolliePaymentLink());

        $this->assertSame('["ideal","creditcard"]', $spyMolliePaymentLinkEntity->getPaymentMethods());
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    protected function createMolliePaymentLinkTransfer(): MolliePaymentLinkTransfer
    {
        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();
        $molliePaymentLinkTransfer
            ->setId('pl_4Y0eZitmBnQ6IDoMqZQKh')
            ->setDescription('Deposit')
            ->setStatus('open')
            ->setExpiresAt('2026-12-31 00:00:00')
            ->setCurrencyCode(static::CURRENCY_CODE);

        return $molliePaymentLinkTransfer;
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapperInterface
     */
    protected function createMolliePaymentLinkMapper(): MolliePaymentLinkMapperInterface
    {
        $molliePaymentLinkMapper = $this->createMolliePersistenceFactory()->createMolliePaymentLinkMapper();

        return $molliePaymentLinkMapper;
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory
     */
    protected function createMolliePersistenceFactory(): MolliePersistenceFactory
    {
        $molliePersistenceFactory = new MolliePersistenceFactory();

        return $molliePersistenceFactory;
    }
}

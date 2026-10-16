<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieMandateApiResponseTransfer;
use Generated\Shared\Transfer\MollieMandateTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SavedCardRemover;

class SavedCardRemoverTest extends Unit
{
    /**
     * @var int
     */
    protected const ID_CUSTOMER = 42;

    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @var string
     */
    protected const MANDATE_ID = 'mdt_pWUnw6pkBN';

    /**
     * @return void
     */
    public function testRemoveSavedCardRevokesMandateOfTheCustomer(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())
            ->method('revokeCustomerMandate')
            ->with($this->callback(function (MollieApiRequestTransfer $mollieApiRequestTransfer): bool {
                return $mollieApiRequestTransfer->getMollieCustomerId() === static::MOLLIE_CUSTOMER_ID
                    && $mollieApiRequestTransfer->getMandateId() === static::MANDATE_ID;
            }))
            ->willReturn((new MollieMandateApiResponseTransfer())->setIsSuccessful(true));

        $response = $this->createRemover($mollieClientMock, $this->createMollieCustomerTransfer(), [static::MANDATE_ID])
            ->removeSavedCard($this->createRequestTransfer());

        $this->assertTrue($response->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testRemoveSavedCardRejectsMandateThatIsNotOneOfTheCustomerCards(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->never())->method('revokeCustomerMandate');

        $response = $this->createRemover($mollieClientMock, $this->createMollieCustomerTransfer(), ['mdt_otherCard'])
            ->removeSavedCard($this->createRequestTransfer());

        $this->assertFalse($response->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testRemoveSavedCardFailsWhenCustomerHasNoMollieCustomer(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->never())->method('revokeCustomerMandate');

        $response = $this->createRemover($mollieClientMock, null, [])
            ->removeSavedCard($this->createRequestTransfer());

        $this->assertFalse($response->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testRemoveSavedCardFailsWhenMollieRejectsTheRevoke(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())
            ->method('revokeCustomerMandate')
            ->willReturn((new MollieMandateApiResponseTransfer())->setIsSuccessful(false)->setCode(422));

        $response = $this->createRemover($mollieClientMock, $this->createMollieCustomerTransfer(), [static::MANDATE_ID])
            ->removeSavedCard($this->createRequestTransfer());

        $this->assertFalse($response->getIsSuccessful());
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClientMock
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer|null $mollieCustomerTransfer
     * @param list<string> $savedMandateIds
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\SavedCardRemover
     */
    protected function createRemover(
        MollieClientInterface $mollieClientMock,
        ?MollieCustomerTransfer $mollieCustomerTransfer,
        array $savedMandateIds,
    ): SavedCardRemover {
        $mollieCustomerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $mollieCustomerProviderMock->method('findMollieCustomer')->willReturn($mollieCustomerTransfer);

        $savedCards = new ArrayObject();
        foreach ($savedMandateIds as $savedMandateId) {
            $savedCards->append((new MollieMandateTransfer())->setId($savedMandateId));
        }

        $savedCardReaderMock = $this->createMock(SavedCardReaderInterface::class);
        $savedCardReaderMock->method('getSavedCards')->willReturn($savedCards);

        return new SavedCardRemover($mollieClientMock, $mollieCustomerProviderMock, $savedCardReaderMock);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    protected function createMollieCustomerTransfer(): MollieCustomerTransfer
    {
        return (new MollieCustomerTransfer())
            ->setFkCustomer(static::ID_CUSTOMER)
            ->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer
     */
    protected function createRequestTransfer(): MollieSavedCardRemoveRequestTransfer
    {
        return (new MollieSavedCardRemoveRequestTransfer())
            ->setIdCustomer(static::ID_CUSTOMER)
            ->setMandateId(static::MANDATE_ID);
    }
}

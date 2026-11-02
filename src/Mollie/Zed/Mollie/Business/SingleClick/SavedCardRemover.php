<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Spryker\Shared\Log\LoggerTrait;

class SavedCardRemover implements SavedCardRemoverInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const MESSAGE_SAVED_CARD_NOT_FOUND = 'Saved card not found for the customer.';

    /**
     * @var string
     */
    protected const MESSAGE_SAVED_CARD_NOT_REVOKED = 'Saved card could not be revoked at Mollie.';

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface $savedCardReader
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieCustomerProviderInterface $mollieCustomerProvider,
        protected SavedCardReaderInterface $savedCardReader,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    public function removeSavedCard(
        MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer,
    ): MollieSavedCardRemoveResponseTransfer {
        $idCustomer = $mollieSavedCardRemoveRequestTransfer->getIdCustomerOrFail();
        $mandateId = $mollieSavedCardRemoveRequestTransfer->getMandateIdOrFail();

        $mollieCustomerTransfer = $this->mollieCustomerProvider->findMollieCustomer(
            (new CustomerTransfer())->setIdCustomer($idCustomer),
        );

        if (!$mollieCustomerTransfer || !$this->isCustomerSavedCard($mollieCustomerTransfer, $mandateId)) {
            $this->getLogger()->warning('Mollie single-click: saved card removal rejected, the card does not belong to the customer.', [
                'idCustomer' => $idCustomer,
                'mandateId' => $mandateId,
            ]);

            return $this->createResponse(false, static::MESSAGE_SAVED_CARD_NOT_FOUND);
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerIdOrFail())
            ->setMandateId($mandateId);

        $mollieMandateApiResponseTransfer = $this->mollieClient->revokeCustomerMandate($mollieApiRequestTransfer);

        if (!$mollieMandateApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->error('Mollie single-click: saved card could not be removed.', [
                'idCustomer' => $idCustomer,
                'mollieCustomerId' => $mollieCustomerTransfer->getMollieCustomerId(),
                'mandateId' => $mandateId,
                'code' => $mollieMandateApiResponseTransfer->getCode(),
                'message' => $mollieMandateApiResponseTransfer->getMessage(),
            ]);

            return $this->createResponse(false, static::MESSAGE_SAVED_CARD_NOT_REVOKED);
        }

        $this->getLogger()->info('Mollie single-click: saved card removed.', [
            'idCustomer' => $idCustomer,
            'mollieCustomerId' => $mollieCustomerTransfer->getMollieCustomerId(),
            'mandateId' => $mandateId,
        ]);

        return $this->createResponse(true);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     * @param string $mandateId
     *
     * @return bool
     */
    protected function isCustomerSavedCard(MollieCustomerTransfer $mollieCustomerTransfer, string $mandateId): bool
    {
        foreach ($this->savedCardReader->getSavedCards($mollieCustomerTransfer) as $mollieMandateTransfer) {
            if ($mollieMandateTransfer->getId() === $mandateId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $isSuccessful
     * @param string|null $message
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    protected function createResponse(bool $isSuccessful, ?string $message = null): MollieSavedCardRemoveResponseTransfer
    {
        return (new MollieSavedCardRemoveResponseTransfer())
            ->setIsSuccessful($isSuccessful)
            ->setMessage($message);
    }
}

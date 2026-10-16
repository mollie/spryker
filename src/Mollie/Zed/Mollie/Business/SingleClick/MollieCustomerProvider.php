<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use DateTime;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;

class MollieCustomerProvider implements MollieCustomerProviderInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieRepositoryInterface $repository,
        protected MollieEntityManagerInterface $entityManager,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function findMollieCustomer(CustomerTransfer $customerTransfer): ?MollieCustomerTransfer
    {
        $idCustomer = $customerTransfer->getIdCustomer();
        if (!$idCustomer) {
            return null;
        }

        return $this->repository->findMollieCustomer($idCustomer, $this->config->getMollieMode());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function getOrCreateMollieCustomer(QuoteTransfer $quoteTransfer): ?MollieCustomerTransfer
    {
        $customerTransfer = $quoteTransfer->getCustomerOrFail();
        $mollieCustomerTransfer = $this->findMollieCustomer($customerTransfer);

        if ($mollieCustomerTransfer) {
            $mollieCustomerTransfer = $this->entityManager->saveMollieCustomer($this->addConsent($mollieCustomerTransfer));

            return $mollieCustomerTransfer->setIsNew(false);
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())->setQuote($quoteTransfer);
        $mollieCustomerApiResponseTransfer = $this->mollieClient->createCustomer($mollieApiRequestTransfer);

        if (!$mollieCustomerApiResponseTransfer->getIsSuccessful() || !$mollieCustomerApiResponseTransfer->getMollieCustomer()?->getMollieCustomerId()) {
            $this->getLogger()->error('Mollie single-click: Mollie customer could not be created.', [
                'idCustomer' => $customerTransfer->getIdCustomer(),
                'code' => $mollieCustomerApiResponseTransfer->getCode(),
                'message' => $mollieCustomerApiResponseTransfer->getMessage(),
            ]);

            return null;
        }

        $mollieCustomerTransfer = (new MollieCustomerTransfer())
            ->setMollieCustomerId($mollieCustomerApiResponseTransfer->getMollieCustomerOrFail()->getMollieCustomerId())
            ->setFkCustomer($customerTransfer->getIdCustomerOrFail())
            ->setMode($this->config->getMollieMode());

        $mollieCustomerTransfer = $this->entityManager->saveMollieCustomer($this->addConsent($mollieCustomerTransfer));

        return $mollieCustomerTransfer->setIsNew(true);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return void
     */
    public function deleteMollieCustomer(MollieCustomerTransfer $mollieCustomerTransfer): void
    {
        $this->entityManager->deleteMollieCustomer($mollieCustomerTransfer->getIdMollieCustomerOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    protected function addConsent(MollieCustomerTransfer $mollieCustomerTransfer): MollieCustomerTransfer
    {
        return $mollieCustomerTransfer
            ->setConsentGivenAt((new DateTime())->format(static::DATE_TIME_FORMAT))
            ->setConsentVersion($this->config->getSingleClickConsentVersion());
    }
}

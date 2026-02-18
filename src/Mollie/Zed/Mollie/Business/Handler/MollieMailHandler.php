<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Zed\Mollie\Communication\Plugin\Oms\Command\MolliePaymentConfirmationMailTypeBuilderPlugin;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface;

class MollieMailHandler implements MollieMailHandlerInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface $mailFacade
     */
    public function __construct(
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieToMailFacadeInterface $mailFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendPaymentConfirmationMail(OrderTransfer $orderTransfer): void
    {
        $localeTransfer = $this->getLocaleTransfer($orderTransfer);

        $mailTransfer = (new MailTransfer())
            ->setOrder($orderTransfer)
            ->setType(MolliePaymentConfirmationMailTypeBuilderPlugin::MAIL_TYPE)
            ->setLocale($localeTransfer)
            ->setStoreName($orderTransfer->getStore());

        $this->mailFacade->handleMail($mailTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    protected function getLocaleTransfer(OrderTransfer $orderTransfer): LocaleTransfer
    {
        if ($orderTransfer->getLocale()) {
            return $this->localeFacade->getLocaleById($orderTransfer->getLocale()->getIdLocale());
        }

        return $this->localeFacade->getCurrentLocale();
    }
}

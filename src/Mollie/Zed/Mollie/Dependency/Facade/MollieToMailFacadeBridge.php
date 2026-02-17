<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;

class MollieToMailFacadeBridge implements MollieToMailFacadeInterface
{
    protected MailFacadeInterface $mailFacade;

    /**
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     */
    public function __construct($mailFacade)
    {
        $this->mailFacade = $mailFacade;
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function handleMail(OrderTransfer $orderTransfer): void
    {
        $this->mailFacade->handleMail($orderTransfer);
    }
}

<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTemplateTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MailExtension\Dependency\Plugin\MailTypeBuilderPluginInterface;

class MolliePaymentConfirmationMailTypeBuilderPlugin extends AbstractPlugin implements MailTypeBuilderPluginInterface
{
    /**
     * @var string
     */
    public const MAIL_TYPE = 'payment confirmation mail';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_HTML = 'mollie/mail/payment_confirmation.html.twig';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_TEXT = 'mollie/mail/payment_confirmation.text.twig';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_MAIL_SUBJECT = 'mollie.mail.payment.confirmation.subject';

    /**
     * {@inheritDoc}
     * - Returns the name of mail for an order confirmation mail.
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * {@inheritDoc}
     * - Builds the `MailTransfer` with data for an order confirmation mail.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    public function build(MailTransfer $mailTransfer): MailTransfer
    {
        /** @var \Generated\Shared\Transfer\OrderTransfer $orderTransfer */
        $orderTransfer = $mailTransfer->getOrderOrFail();

        return $mailTransfer
            ->setSubject(static::GLOSSARY_KEY_MAIL_SUBJECT)
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_HTML)
                    ->setIsHtml(true),
            )
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_TEXT)
                    ->setIsHtml(false),
            )
            ->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail($orderTransfer->getEmailOrFail())
                    ->setName(sprintf('%s %s', $orderTransfer->getFirstName(), $orderTransfer->getLastName())),
            );
    }
}

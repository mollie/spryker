<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MolliePayPalPaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MolliePayPalSubForm extends AbstractMollieSubForm
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'paypal';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'data_class' => MolliePayPalPaymentTransfer::class,
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_PAYPAL . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_PAYPAL;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_PAYPAL;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_PAYPAL;
    }
}

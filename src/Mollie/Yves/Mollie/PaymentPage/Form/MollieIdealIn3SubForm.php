<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieIdealIn3PaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieIdealIn3SubForm extends AbstractMollieSubForm
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'in3';

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
                'data_class' => MollieIdealIn3PaymentTransfer::class,
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_IDEAL_IN3 . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_IDEAL_IN3;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_IDEAL_IN3;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_IDEAL_IN3;
    }
}

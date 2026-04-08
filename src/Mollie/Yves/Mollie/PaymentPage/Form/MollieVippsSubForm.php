<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Mollie\Shared\Mollie\MollieConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieVippsSubForm extends AbstractMollieSubForm
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'vipps';

    /**
     * @var string
     */
    protected const LOGO_URL = 'logoUrl';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_VIPPS_PROVIDER . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_VIPPS;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_VIPPS;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_VIPPS_PROVIDER;
    }
}

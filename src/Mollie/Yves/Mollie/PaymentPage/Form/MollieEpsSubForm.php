<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Mollie\Shared\Mollie\MollieConfig;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieEpsSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'eps';

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
        return MollieConfig::MOLLIE_PROVIDER_EPS . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_EPS;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_EPS;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_EPS;
    }
}

<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieApplePayPaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieApplePaySubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'applePay';

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
            ->setDefaults([
                'data_class' => MollieApplePayPaymentTransfer::class,
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $optionLogoKey = $this->getPropertyPath() . MollieConstants::LOGO_URL;
        $view->vars[static::LOGO_URL] = $options['select_options'][$optionLogoKey];
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_APPLE_PAY . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_APPLE_PAY;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_APPLE_PAY;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_APPLE_PAY;
    }
}

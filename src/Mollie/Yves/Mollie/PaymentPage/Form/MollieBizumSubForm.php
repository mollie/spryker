<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MollieBizumSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'bizum';

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
        return MollieConfig::MOLLIE_PROVIDER_BIZUM . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_BIZUM;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_BIZUM;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_BIZUM;
    }
}

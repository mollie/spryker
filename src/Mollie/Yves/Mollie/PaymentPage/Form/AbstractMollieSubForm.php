<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

abstract class AbstractMollieSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const LOGO_URL = 'logoUrl';

    /**
     * @var string
     */
    protected const IS_LOGO_VISIBLE = 'isLogoVisible';

    /**
     * @var bool
     */
    protected const DEFAULT_LOGO_VISIBLE = true;

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
        $isLogoVisibleKey = $this->getPropertyPath() . MollieConstants::IS_LOGO_VISIBLE;
        $view->vars[static::LOGO_URL] = $options['select_options'][$optionLogoKey];
        $view->vars[static::IS_LOGO_VISIBLE] = $options['select_options'][$isLogoVisibleKey] ?? static::DEFAULT_LOGO_VISIBLE;
    }
}

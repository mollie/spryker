<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class SingleClickConfigForm extends AbstractType
{
    public const string FIELD_IS_ENABLED = 'is_enabled';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FIELD_IS_ENABLED, CheckboxType::class, [
            'label' => 'Enable single-click credit card payments (saved cards)',
            'required' => false,
        ]);
    }
}

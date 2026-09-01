<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Mollie\Shared\Mollie\MollieConfig;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpressCheckoutConfigForm extends AbstractType
{
    public const string OPTION_EXPRESS_METHODS = 'express_methods';

    /**
     * @var array<string, string>
     */
    protected const METHOD_LABELS = [
        MollieConfig::EXPRESS_METHOD_APPLE_PAY => 'Apple Pay Express',
        MollieConfig::EXPRESS_METHOD_GOOGLE_PAY => 'Google Pay Express',
        MollieConfig::EXPRESS_METHOD_PAYPAL => 'PayPal Express',
    ];

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::OPTION_EXPRESS_METHODS);
        $resolver->setAllowedTypes(static::OPTION_EXPRESS_METHODS, 'array');
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options[static::OPTION_EXPRESS_METHODS] as $expressMethod) {
            $builder->add($expressMethod, CheckboxType::class, [
                'label' => static::METHOD_LABELS[$expressMethod] ?? ucwords(str_replace(['_', '-'], ' ', $expressMethod)),
                'required' => false,
            ]);
        }
    }
}

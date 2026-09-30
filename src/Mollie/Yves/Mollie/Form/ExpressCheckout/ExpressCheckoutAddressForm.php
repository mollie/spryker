<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Form\ExpressCheckout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpressCheckoutAddressForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_BILLING_ADDRESS = 'billingAddress';

    /**
     * @var string
     */
    public const FIELD_SHIPPING_ADDRESS = 'shippingAddress';

    /**
     * @var string
     */
    public const FIELD_SHIPPING_SAME_AS_BILLING = 'shippingSameAsBilling';

    /**
     * @var string
     */
    public const OPTION_COUNTRY_CHOICES = ExpressCheckoutAddressType::OPTION_COUNTRY_CHOICES;

    /**
     * @var string
     */
    protected const VALIDATION_GROUP_SHIPPING_ADDRESS = 'shippingAddress';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'mollieExpressCheckoutAddressForm';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::OPTION_COUNTRY_CHOICES);
        $resolver->setDefaults([
            // The shipping address is only validated when it differs from the billing address.
            'validation_groups' => function (FormInterface $form): array {
                if ($form->get(static::FIELD_SHIPPING_SAME_AS_BILLING)->getData()) {
                    return ['Default'];
                }

                return ['Default', static::VALIDATION_GROUP_SHIPPING_ADDRESS];
            },
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(static::FIELD_BILLING_ADDRESS, ExpressCheckoutAddressType::class, [
                'label' => 'Billing address',
                ExpressCheckoutAddressType::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
            ])
            ->add(static::FIELD_SHIPPING_SAME_AS_BILLING, CheckboxType::class, [
                'label' => 'Shipping address is the same as the billing address',
                'required' => false,
            ])
            ->add(static::FIELD_SHIPPING_ADDRESS, ExpressCheckoutAddressType::class, [
                'label' => 'Shipping address',
                'required' => false,
                ExpressCheckoutAddressType::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
                ExpressCheckoutAddressType::OPTION_VALIDATION_GROUP => static::VALIDATION_GROUP_SHIPPING_ADDRESS,
            ]);
    }
}

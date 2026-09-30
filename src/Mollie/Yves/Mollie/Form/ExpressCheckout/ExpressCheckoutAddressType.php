<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Form\ExpressCheckout;

use Generated\Shared\Transfer\AddressTransfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExpressCheckoutAddressType extends AbstractType
{
    /**
     * @var string
     */
    public const OPTION_COUNTRY_CHOICES = 'country_choices';

    /**
     * @var string
     */
    public const OPTION_VALIDATION_GROUP = 'address_validation_group';

    /**
     * @var array<string, string>
     */
    protected const SALUTATION_CHOICES = [
        'Mr' => 'Mr',
        'Ms' => 'Ms',
        'Mrs' => 'Mrs',
        'Dr' => 'Dr',
    ];

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressTransfer::class,
            static::OPTION_VALIDATION_GROUP => 'Default',
        ]);
        $resolver->setRequired(static::OPTION_COUNTRY_CHOICES);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requiredConstraints = [new NotBlank(['groups' => [$options[static::OPTION_VALIDATION_GROUP]]])];

        $builder
            ->add(AddressTransfer::SALUTATION, ChoiceType::class, [
                'label' => 'Salutation',
                'choices' => static::SALUTATION_CHOICES,
                'constraints' => $requiredConstraints,
            ])
            ->add(AddressTransfer::FIRST_NAME, TextType::class, ['label' => 'First name', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::LAST_NAME, TextType::class, ['label' => 'Last name', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::COMPANY, TextType::class, ['label' => 'Company', 'required' => false])
            ->add(AddressTransfer::ADDRESS1, TextType::class, ['label' => 'Street', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::ADDRESS2, TextType::class, ['label' => 'House number', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::ZIP_CODE, TextType::class, ['label' => 'Zip code', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::CITY, TextType::class, ['label' => 'City', 'constraints' => $requiredConstraints])
            ->add(AddressTransfer::ISO2_CODE, ChoiceType::class, [
                'label' => 'Country',
                'choices' => $options[static::OPTION_COUNTRY_CHOICES],
                'constraints' => $requiredConstraints,
            ])
            ->add(AddressTransfer::PHONE, TextType::class, ['label' => 'Phone', 'required' => false]);
    }
}

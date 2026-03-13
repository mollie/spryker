<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;

class PaymentLinkForm extends AbstractType
{
    public const FIELD_TYPE = 'type';
    public const FIELD_CURRENCY = 'currency';
    public const FIELD_AMOUNT = 'amount';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_EXPIRY_DATE = 'expiryDate';
    public const FIELD_REDIRECT_URL = 'redirectUrl';
    public const FIELD_SAVE_REDIRECT_URL = 'saveRedirectUrl';
    public const FIELD_IS_REUSABLE = 'isReusable';
    public const FIELD_PAYMENT_METHODS = 'paymentMethods';

    public const OPTION_PAYMENT_TYPES = 'payment_types';
    public const OPTION_CURRENCIES = 'currencies';
    public const OPTION_AVAILABLE_PAYMENT_METHODS = 'available_payment_methods';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            self::OPTION_PAYMENT_TYPES => $this->getPaymentTypes(),
            self::OPTION_CURRENCIES => $this->getCurrencies(),
            self::OPTION_AVAILABLE_PAYMENT_METHODS => $this->getAvailablePaymentMethods(),
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addTypeField($builder, $options)
            ->addCurrencyField($builder, $options)
            ->addAmountField($builder)
            ->addDescriptionField($builder)
            ->addExpiryDateField($builder)
            ->addRedirectUrlField($builder)
            ->addSaveRedirectUrlField($builder)
            ->addIsReusableField($builder)
            ->addPaymentMethodsField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addTypeField(FormBuilderInterface $builder, array $options): static
    {
        $builder->add(self::FIELD_TYPE, ChoiceType::class, [
            'label' => 'Type',
            'choices' => $options[self::OPTION_PAYMENT_TYPES],
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addCurrencyField(FormBuilderInterface $builder, array $options): static
    {
        $builder->add(self::FIELD_CURRENCY, ChoiceType::class, [
            'label' => 'Currency',
            'choices' => $options[self::OPTION_CURRENCIES],
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAmountField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_AMOUNT, NumberType::class, [
            'label' => 'Amount',
            'required' => true,
            'scale' => 2,
            'constraints' => [
                new NotBlank(),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Amount must be greater than 0',
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDescriptionField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_DESCRIPTION, TextareaType::class, [
            'label' => 'Description',
            'required' => true,
            'attr' => [
                'rows' => 3,
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    'max' => 500,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addExpiryDateField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_EXPIRY_DATE, DateType::class, [
            'label' => 'Expiry date (optional)',
            'required' => false,
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'placeholder' => 'DD/MM/YYYY',
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addRedirectUrlField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_REDIRECT_URL, TextType::class, [
            'label' => 'Redirect URL (optional)',
            'required' => false,
            'attr' => [
                'placeholder' => 'https://example.com/thank-you',
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSaveRedirectUrlField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_SAVE_REDIRECT_URL, CheckboxType::class, [
            'label' => 'Save URL for all future links',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIsReusableField(FormBuilderInterface $builder): static
    {
        $builder->add(self::FIELD_IS_REUSABLE, CheckboxType::class, [
            'label' => 'Reusable - Create a reusable payment link that can be paid multiple times',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addPaymentMethodsField(FormBuilderInterface $builder, array $options): static
    {
        $builder->add(self::FIELD_PAYMENT_METHODS, ChoiceType::class, [
            'label' => 'Payment methods',
            'choices' => $options[self::OPTION_AVAILABLE_PAYMENT_METHODS],
            'multiple' => true,
            'required' => false,
            'expanded' => false,
            'attr' => [
                'class' => 'select2',
                'data-placeholder' => 'By default, all methods are offered in your checkout.',
            ],
        ]);

        return $this;
    }

    /**
     * @return array
     */
    protected function getPaymentTypes(): array
    {
        return [
            'Fixed' => 'fixed',
            'Open' => 'open',
        ];
    }

    /**
     * @return array
     */
    protected function getCurrencies(): array
    {
        return [
            'EUR' => 'EUR',
            'USD' => 'USD',
            'GBP' => 'GBP',
            'CHF' => 'CHF',
            'PLN' => 'PLN',
            'SEK' => 'SEK',
            'NOK' => 'NOK',
            'DKK' => 'DKK',
            'CZK' => 'CZK',
            'HUF' => 'HUF',
            'RON' => 'RON',
        ];
    }

    /**
     * @return array
     */
    protected function getAvailablePaymentMethods(): array
    {
        // This should ideally come from Mollie API or config
        return [
            'Credit Card' => 'creditcard',
            'iDEAL' => 'ideal',
            'PayPal' => 'paypal',
            'Bancontact' => 'bancontact',
            'SEPA Direct Debit' => 'directdebit',
            'Bank Transfer' => 'banktransfer',
            'Belfius Pay Button' => 'belfius',
            'KBC/CBC Payment Button' => 'kbc',
            'Klarna Pay Later' => 'klarnapaylater',
            'Klarna Slice It' => 'klarnasliceit',
            'Przelewy24' => 'przelewy24',
            'Giropay' => 'giropay',
            'EPS' => 'eps',
            'Apple Pay' => 'applepay',
            'Paysafecard' => 'paysafecard',
        ];
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'mollie_payment_link';
    }
}

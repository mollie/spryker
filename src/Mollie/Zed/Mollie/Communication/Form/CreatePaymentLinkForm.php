<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreatePaymentLinkForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_TYPE = 'type';

    /**
     * @var string
     */
    public const FIELD_CURRENCY = 'currency';

    /**
     * @var string
     */
    public const FIELD_AMOUNT = 'amount';

    /**
     * @var string
     */
    public const FIELD_DESCRIPTION = 'description';

    /**
     * @var string
     */
    public const FIELD_EXPIRY_DATE = 'expiryDate';

    /**
     * @var string
     */
    public const FIELD_REDIRECT_URL = 'redirectUrl';

    /**
     * @var string
     */
    public const FIELD_SAVE_REDIRECT_URL = 'saveRedirectUrl';

    /**
     * @var string
     */
    public const FIELD_IS_REUSABLE = 'isReusable';

    /**
     * @var string
     */
    public const FIELD_PAYMENT_METHODS = 'paymentMethods';

    /**
     * @var string
     */
    public const OPTION_CURRENCY_CODES = 'currency_codes';

    /**
     * @var string
     */
    public const OPTION_AVAILABLE_PAYMENT_METHODS = 'available_payment_methods';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefined([
            static::OPTION_AVAILABLE_PAYMENT_METHODS,
            static::OPTION_CURRENCY_CODES,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addCurrencyField($builder, $options)
            ->addAmountField($builder)
            ->addDescriptionField($builder)
            ->addExpiryDateField($builder)
            ->addRedirectUrlField($builder)
            ->addIsReusableField($builder)
            ->addPaymentMethodsField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addCurrencyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_CURRENCY, ChoiceType::class, [
            'label' => 'Currency',
            'choices' => $options[static::OPTION_CURRENCY_CODES],
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
    protected function addAmountField(FormBuilderInterface $builder)
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
    protected function addDescriptionField(FormBuilderInterface $builder)
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
    protected function addExpiryDateField(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_EXPIRY_DATE, DateTimeType::class, [
            'label' => 'Expiry date (optional)',
            'required' => false,
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'placeholder' => 'DD/MM/YYYY',
                'class' => 'datepicker js-expiry-date safe-datetime',
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addRedirectUrlField(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_REDIRECT_URL, TextType::class, [
            'label' => 'Redirect URL (optional)',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIsReusableField(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_IS_REUSABLE, CheckboxType::class, [
            'label' => 'Reusable - Create a reusable payment link that can be paid multiple times',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addPaymentMethodsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_PAYMENT_METHODS, ChoiceType::class, [
            'label' => 'Payment methods (Optional)',
            'choices' => array_unique($options[self::OPTION_AVAILABLE_PAYMENT_METHODS]),
            'choice_value' => function ($choice) {
                return $choice;
            },
            'multiple' => true,
            'required' => false,
            'expanded' => false,
            'attr' => [
                'class' => '',
                'data-placeholder' => 'By default, all methods are offered in your checkout.',
            ],
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'mollie_payment_link';
    }
}

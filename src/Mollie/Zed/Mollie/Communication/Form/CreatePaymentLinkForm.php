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
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
    public const FIELD_MINIMUM_AMOUNT = 'minimumAmount';

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
     * @var array<int, string>
     */
    protected const REDIRECT_URL_ALLOWED_PROTOCOLS = ['http', 'https'];

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
        $resolver->setDefaults([
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'validateAmountOrMinimumAmount'],
                ]),
            ],
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
            ->addMinimumAmountField($builder)
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
            'label' => 'Amount (leave empty to let the customer enter the amount)',
            'required' => false,
            'scale' => 2,
            'constraints' => [
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
    protected function addMinimumAmountField(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_MINIMUM_AMOUNT, NumberType::class, [
            'label' => 'Minimum amount (only when amount is empty)',
            'required' => false,
            'scale' => 2,
            'constraints' => [
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Minimum amount must be greater than 0',
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param array<string, mixed> $formData
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *
     * @return void
     */
    public function validateAmountOrMinimumAmount(array $formData, ExecutionContextInterface $context): void
    {
        $amount = $formData[static::FIELD_AMOUNT] ?? null;
        $minimumAmount = $formData[static::FIELD_MINIMUM_AMOUNT] ?? null;

        if ($amount !== null && $minimumAmount !== null) {
            $context->buildViolation('Fill in either an amount or a minimum amount, not both')
                ->atPath(sprintf('[%s]', static::FIELD_MINIMUM_AMOUNT))
                ->addViolation();

            return;
        }

        if ($amount === null && $minimumAmount === null) {
            $context->buildViolation('Fill in an amount or a minimum amount')
                ->atPath(sprintf('[%s]', static::FIELD_AMOUNT))
                ->addViolation();
        }
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
            'constraints' => [
                new Url([
                    'protocols' => static::REDIRECT_URL_ALLOWED_PROTOCOLS,
                    'message' => 'Redirect URL must be a valid http or https URL',
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

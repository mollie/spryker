<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Zed\Mollie\Communication\DataProvider\MolliePaymentMethodsDataProvider;
use Mollie\Zed\Mollie\Communication\Form\DataTransformer\AmountTransferTransformer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\MoneyGui\Communication\Form\Type\SimpleMoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class PaymentMethodConfigForm extends AbstractType
{
    public const string FIELD_IMAGE = 'image';

    public const string FIELD_MOLLIE_ID = 'mollieId';

    public const string FIELD_IS_ACTIVE = 'isActive';

    public const string FIELD_IS_LOGO_VISIBLE = 'isLogoVisible';

    public const string FIELD_MINIMUM_AMOUNT = 'minimumAmount';

    public const string FIELD_MAXIMUM_AMOUNT = 'maximumAmount';

    public const string WARNING_MINIMUM_AMOUNT = 'mollie.payment-method.warning.minimum-amount';

    public const string WARNING_MAXIMUM_AMOUNT = 'mollie.payment-method.warning.maximum-amount';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            MolliePaymentMethodsDataProvider::VALIDATION_MAXIMUM_VALUE,
            MolliePaymentMethodsDataProvider::VALIDATION_MINIMUM_VALUE,
        ]);

        $resolver->setDefaults([
            'data_class' => MolliePaymentMethodConfigTransfer::class,
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
            ->addMinimumAmountField($builder, $options)
            ->addMaximumAmountField($builder, $options)
            ->addLogoField($builder, $options)
            ->addMollieIdField($builder, $options)
            ->addIsActiveField($builder, $options)
            ->addIsLogoVisibleField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addMinimumAmountField(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];

        if (isset($options[MolliePaymentMethodsDataProvider::VALIDATION_MINIMUM_VALUE])) {
            $constraints[] = new Callback([
                'callback' => function (mixed $value, ExecutionContextInterface $context) use ($options): void {
                    $minimum = $options[MolliePaymentMethodsDataProvider::VALIDATION_MINIMUM_VALUE];
                    $maximum = $options[MolliePaymentMethodsDataProvider::VALIDATION_MAXIMUM_VALUE];
                    $amount = $value->getValue();

                    $isMinimumAmountValidationFailed = $amount < $minimum;
                    if ($maximum) {
                        $isMinimumAmountValidationFailed = $isMinimumAmountValidationFailed || $amount > $maximum;
                    }

                    $maximum = $maximum ?: 'unlimited';
                    if ($isMinimumAmountValidationFailed) {
                        $errorMessage = sprintf(
                            $this->getFactory()->getTranslatorFacade()->trans(static::WARNING_MINIMUM_AMOUNT),
                            $maximum,
                            $minimum,
                        );
                        $context->addViolation($errorMessage);
                    }
                },
            ]);
        }

        $builder->add(static::FIELD_MINIMUM_AMOUNT, SimpleMoneyType::class, [
            'label' => 'Minimum amount',
            'constraints' => $constraints,
            'required' => true,
        ]);

        $builder->get(static::FIELD_MINIMUM_AMOUNT)->resetModelTransformers()->addModelTransformer(new AmountTransferTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addMaximumAmountField(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];

        if (isset($options[MolliePaymentMethodsDataProvider::VALIDATION_MAXIMUM_VALUE])) {
            $constraints[] = new Callback([
                'callback' => function (mixed $value, ExecutionContextInterface $context) use ($options): void {
                    $minimum = $options[MolliePaymentMethodsDataProvider::VALIDATION_MINIMUM_VALUE];
                    $maximum = $options[MolliePaymentMethodsDataProvider::VALIDATION_MAXIMUM_VALUE];
                    $amount = $value->getValue();

                    $isMaximumAmountValidationFailed = $amount < $minimum;
                    if ($maximum) {
                        $isMaximumAmountValidationFailed = $isMaximumAmountValidationFailed || $amount > $maximum;
                    }

                    $maximum = $maximum ?: 'unlimited';
                    if ($isMaximumAmountValidationFailed) {
                        $errorMessage = sprintf(
                            $this->getFactory()->getTranslatorFacade()->trans(static::WARNING_MAXIMUM_AMOUNT),
                            $maximum,
                            $minimum,
                        );
                        $context->addViolation($errorMessage);
                    }
                },
            ]);
        }

        $builder->add(static::FIELD_MAXIMUM_AMOUNT, SimpleMoneyType::class, [
            'label' => 'Maximum amount',
            'constraints' => $constraints,
            'required' => true,
        ]);

        $builder->get(static::FIELD_MAXIMUM_AMOUNT)->addModelTransformer(new AmountTransferTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addLogoField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_IMAGE, TextType::class, [
            'label' => 'Logo',
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return $this
     */
    protected function addIsActiveField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_IS_ACTIVE, CheckboxType::class, [
            'label' => 'is active',
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
    protected function addIsLogoVisibleField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_IS_LOGO_VISIBLE, CheckboxType::class, [
            'label' => 'is logo visible on checkout',
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
    protected function addMollieIdField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_MOLLIE_ID, HiddenType::class, [
            'label' => 'Logo',
            'constraints' => [
                new NotBlank(),
            ],
            'required' => true,
        ]);

        return $this;
    }
}

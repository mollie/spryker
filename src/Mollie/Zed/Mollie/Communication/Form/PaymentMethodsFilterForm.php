<?php


namespace Mollie\Zed\Mollie\Communication\Form;

use Mollie\Zed\Mollie\Communication\Form\DataProvider\PaymentMethodsFilterFormDataProvider;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentMethodsFilterForm extends AbstractType
{
    protected const string FIELD_CURRENCY = 'currency';

    protected const string LABEL_CURRENCY = 'Currency';

    protected const string FIELD_SHOW_ONLY_ENABLED = 'showOnlyEnabled';

    protected const string LABEL_SHOW_ONLY_ENABLED = 'Show only enabled payment methods';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'currencies',
        ]);

        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);

        $resolver->setRequired([
            PaymentMethodsFilterFormDataProvider::OPTION_CURRENCIES,
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
        $builder->setMethod(Request::METHOD_GET);

        $this->addCurrenciesField($builder, $options);
        $this->addShowOnlyEnabledMethodsField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function addCurrenciesField(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FIELD_CURRENCY, ChoiceType::class, [
            'label' => static::LABEL_CURRENCY,
            'placeholder' => 'Select currency',
            'required' => false,
            'multiple' => false,
            'expanded' => false,
            'choices' => $options['currencies'] ?? [],
            'attr' => [
                'class' => 'spryker-form-select2combobox',
                'data-placeholder' => 'Select currency',
                'data-clearable' => true,
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function addShowOnlyEnabledMethodsField(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FIELD_SHOW_ONLY_ENABLED, CheckboxType::class, [
            'label' => static::LABEL_SHOW_ONLY_ENABLED,
            'false_values' => [null, ''],
            'required' => false,
        ]);
    }
}

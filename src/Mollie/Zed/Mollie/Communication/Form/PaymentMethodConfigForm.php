<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentMethodConfigForm extends AbstractType
{
    public const string FIELD_LOGO = 'logo';

    public const string FIELD_MOLLIE_ID = 'mollieId';

    public const string FIELD_IS_ACTIVE = 'isActive';

    public const string FIELD_IS_LOGO_VISIBLE = 'isLogoVisible';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
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
    protected function addLogoField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_LOGO, TextType::class, [
            'label' => 'Logo',
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

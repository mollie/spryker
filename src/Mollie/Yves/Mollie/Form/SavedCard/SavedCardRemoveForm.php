<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Form\SavedCard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SavedCardRemoveForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_MANDATE_ID = 'mandateId';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'mollieSavedCardRemoveForm';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FIELD_MANDATE_ID, HiddenType::class, [
            'constraints' => [new NotBlank()],
        ]);
    }
}

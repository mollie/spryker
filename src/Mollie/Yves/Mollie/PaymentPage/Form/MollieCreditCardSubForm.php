<?php


declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieCreditCardSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
 /**
  * @var string
  */
    protected const PAYMENT_METHOD = 'creditCard';

    /**
     * @var string
     */
    protected const CARD_TOKEN = 'cardToken';

 /**
  * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
  *
  * @return void
  */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => MollieCreditCardPaymentTransfer::class,
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, string> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('cardToken', HiddenType::class, [
            'required' => true,
             'attr' => [
                'class' => 'card-token',
             ],
            'constraints' => [
                new NotBlank([
                    'groups' => $this->getPropertyPath(),
                    'message' => 'mollie.checkout.payment.credit.card.missing.token',
                ]),
            ],

        ])
        ->add('settings', HiddenType::class, [
            'mapped' => false,
            'attr' => [
                 'class' => 'settings',
                 'data-profile-id' => $this->getConfig()->getProfileId(),
                 'data-test-mode' => $this->getConfig()->isTestMode() ? 'true' : 'false',
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::PROVIDER_NAME . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
         return MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::PROVIDER_NAME;
    }
}

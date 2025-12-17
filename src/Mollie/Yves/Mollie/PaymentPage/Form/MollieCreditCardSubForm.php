<?php


declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Mollie\Yves\Mollie\MollieConfig;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var string
     */
    protected const PAYMENT_ERROR_MESSAGE = 'Payment token is missing. Please complete the payment form.';

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
             'attr' => [
                'class' => 'card-token',
             ],
        ])
        ->add('settings', HiddenType::class, [
            'mapped' => false,
            'attr' => [
                 'class' => 'settings',
                 'data-profile-id' => $this->getConfig()->getProfileId(),
                 'data-test-mode' => $this->getConfig()->isTestMode() ? 'true' : 'false',
            ],
        ])
        ->addEventListener(
            FormEvents::POST_SUBMIT,
            [$this, 'onPostSubmit'],
        );
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @return void
     */
    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();

        $cardToken = $form->getData()->getCardToken();

        if (!$cardToken) {
            $form->get(static::CARD_TOKEN)->addError(
                new FormError(static::PAYMENT_ERROR_MESSAGE),
            );
        }
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

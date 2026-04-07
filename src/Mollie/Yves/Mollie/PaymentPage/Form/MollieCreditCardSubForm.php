<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieCreditCardSubForm extends AbstractMollieSubForm
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
        $isMollieCreditCardComponentEnabled = $this->getConfig()->isMollieCreditCardComponentEnabled();
        if (!$isMollieCreditCardComponentEnabled) {
            return;
        }

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
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['isMollieCreditCardComponentEnabled'] = $this->getConfig()->isMollieCreditCardComponentEnabled();
        $view->vars['profileId'] = $this->getConfig()->getProfileId();
        $view->vars['testMode'] = $this->getConfig()->isTestMode() ? 'true' : 'false';
        $view->vars['jsSrc'] = $this->getConfig()->getMollieCreditCardComponentsJsSrc();
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_CREDIT_CARD . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
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
        return MollieConfig::MOLLIE_PROVIDER_CREDIT_CARD;
    }
}

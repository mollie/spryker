<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use ArrayObject;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MollieMandateTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var string
     */
    protected const FIELD_MANDATE_ID = 'mandateId';

    /**
     * @var string
     */
    protected const FIELD_IS_SAVE_CARD_CONSENT_GIVEN = 'isSaveCardConsentGiven';

    /**
     * @var string
     */
    public const OPTION_SINGLE_CLICK_PAYMENT_DATA = 'mollieCreditCardPaymentSingleClickPaymentData';

    /**
     * @var string
     */
    protected const SAVED_CARD_LABEL_SEPARATOR = ', ';

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
                'constraints' => [
                    new Callback([
                        'callback' => [$this, 'validateCardTokenOrSavedCard'],
                        'groups' => $this->getPropertyPath(),
                    ]),
                ],
            ])
            ->setRequired(static::OPTIONS_FIELD_NAME);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isMollieCreditCardComponentEnabled = $this->getConfig()->isMollieCreditCardComponentEnabled();
        if (!$isMollieCreditCardComponentEnabled) {
            return;
        }

        $builder->add(static::CARD_TOKEN, HiddenType::class, [
            'required' => false,
             'attr' => [
                'class' => 'card-token',
             ],
        ]);

        $selectOptions = $options[static::OPTIONS_FIELD_NAME] ?? [];
        $mollieSingleClickPaymentDataTransfer = $selectOptions[static::OPTION_SINGLE_CLICK_PAYMENT_DATA] ?? null;
        if (!$mollieSingleClickPaymentDataTransfer || !$mollieSingleClickPaymentDataTransfer->getIsEnabled()) {
            return;
        }

        $this->addSavedCardField($builder, $mollieSingleClickPaymentDataTransfer->getSavedCards());
        $this->addSaveCardConsentField($builder);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCreditCardPaymentTransfer|null $mollieCreditCardPaymentTransfer
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *
     * @return void
     */
    public function validateCardTokenOrSavedCard(
        ?MollieCreditCardPaymentTransfer $mollieCreditCardPaymentTransfer,
        ExecutionContextInterface $context,
    ): void {
        if (!$this->getConfig()->isMollieCreditCardComponentEnabled()) {
            return;
        }

        if ($mollieCreditCardPaymentTransfer?->getCardToken() || $mollieCreditCardPaymentTransfer?->getMandateId()) {
            return;
        }

        $context->buildViolation('mollie.checkout.payment.credit.card.missing.token')
            ->atPath(static::CARD_TOKEN)
            ->addViolation();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \ArrayObject<int, \Generated\Shared\Transfer\MollieMandateTransfer> $savedCards
     *
     * @return void
     */
    protected function addSavedCardField(FormBuilderInterface $builder, ArrayObject $savedCards): void
    {
        if (!$savedCards->count()) {
            return;
        }

        $choices = [];
        foreach ($savedCards as $mollieMandateTransfer) {
            $choices[$this->createSavedCardLabel($mollieMandateTransfer)] = $mollieMandateTransfer->getId();
        }

        $builder->add(static::FIELD_MANDATE_ID, ChoiceType::class, [
            'label' => 'mollie.checkout.payment.credit.card.saved.cards.title',
            'choices' => $choices,
            'choice_translation_domain' => false,
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'placeholder' => 'mollie.checkout.payment.credit.card.new.card',
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addSaveCardConsentField(FormBuilderInterface $builder): void
    {
        $builder->add(static::FIELD_IS_SAVE_CARD_CONSENT_GIVEN, CheckboxType::class, [
            'label' => 'mollie.checkout.payment.credit.card.save.card.consent',
            'required' => false,
            'data' => false,
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieMandateTransfer $mollieMandateTransfer
     *
     * @return string
     */
    protected function createSavedCardLabel(MollieMandateTransfer $mollieMandateTransfer): string
    {
        $mollieMandateDetailsTransfer = $mollieMandateTransfer->getDetails();

        $labelParts = array_filter([
            trim(sprintf('%s **** %s', $mollieMandateDetailsTransfer->getCardLabel(), $mollieMandateDetailsTransfer->getCardNumber())),
            $mollieMandateDetailsTransfer->getCardExpiryDate(),
            $mollieMandateDetailsTransfer->getCardHolder(),
        ]);

        return implode(static::SAVED_CARD_LABEL_SEPARATOR, $labelParts);
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

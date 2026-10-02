<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form;

use Generated\Shared\Transfer\MollieApplePayDirectPaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieApplePayDirectSubForm extends AbstractMollieSubForm
{
    /**
     * @var string
     */
    public const OPTION_APPLE_PAY_AMOUNT = 'applePayAmount';

    /**
     * @var string
     */
    public const OPTION_APPLE_PAY_CURRENCY_CODE = 'applePayCurrencyCode';

    /**
     * @var string
     */
    public const OPTION_APPLE_PAY_COUNTRY_CODE = 'applePayCountryCode';

    /**
     * @var string
     */
    protected const PAYMENT_METHOD = 'applePayDirect';

    /**
     * @var string
     */
    protected const FIELD_APPLE_PAY_PAYMENT_TOKEN = 'applePayPaymentToken';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'data_class' => MollieApplePayDirectPaymentTransfer::class,
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
        $builder->add(static::FIELD_APPLE_PAY_PAYMENT_TOKEN, HiddenType::class, [
            'required' => true,
            'attr' => [
                'class' => 'apple-pay-payment-token',
            ],
            'constraints' => [
                new NotBlank([
                    'groups' => $this->getPropertyPath(),
                    'message' => 'mollie.checkout.payment.apple.pay.direct.missing.token',
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
        $selectOptions = $options[static::OPTIONS_FIELD_NAME];
        $view->vars['applePaySdkSrc'] = $this->getConfig()->getApplePaySdkSrc();
        $view->vars['applePayPaymentSessionEndpoint'] = MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_APPLE_PAY_CREATE_PAYMENT_SESSION;
        $view->vars['amount'] = $selectOptions[static::OPTION_APPLE_PAY_AMOUNT];
        $view->vars['currencyCode'] = $selectOptions[static::OPTION_APPLE_PAY_CURRENCY_CODE];
        $view->vars['countryCode'] = $selectOptions[static::OPTION_APPLE_PAY_COUNTRY_CODE];
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_APPLE_PAY_DIRECT . DIRECTORY_SEPARATOR . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_APPLE_PAY_DIRECT;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return MollieConfig::MOLLIE_PAYMENT_APPLE_PAY_DIRECT;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return MollieConfig::MOLLIE_PROVIDER_APPLE_PAY_DIRECT;
    }
}

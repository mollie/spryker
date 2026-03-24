<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Yves\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentApplePayHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentBancontactHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentBankTransferHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentCreditCardHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentEpsHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentIdealHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentKbcHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentKlarnaHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentKlarnaPayLaterHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentKlarnaPayNowHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentKlarnaSliceItHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentMbWayHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentPayByBankHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentPayPalHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentSatispayHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentSwishHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentTrustlyHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentTwintHandler;
use Mollie\Yves\Mollie\Handler\Payment\MolliePaymentVippsHandler;
use Mollie\Yves\Mollie\Mapper\MollieMapper;
use Mollie\Yves\Mollie\Mapper\MollieMapperInterface;
use Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieApplePaySubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieBancontactSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieBankTransferSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieCreditCardSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieEpsSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieIdealSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieKbcSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieKlarnaPayLaterSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieKlarnaPayNowSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieKlarnaSliceItSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieKlarnaSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieMbWaySubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MolliePayByBankSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MolliePayPalSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieSatispaySubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieSwishSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieTrustlySubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieTwintSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieVippsSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieApplePaySubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieBancontactSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieBankTransferSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieCreditCardSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieEpsSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieIdealSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieKbcSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieKlarnaPayLaterSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieKlarnaPayNowSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieKlarnaSliceItSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieKlarnaSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieMbWaySubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MolliePayByBankSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MolliePayPalSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieSatispaySubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieSwishSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieTrustlySubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieTwintSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieVippsSubForm;
use Mollie\Yves\Mollie\Validator\WebhookSignatureValidator;
use Mollie\Yves\Mollie\Validator\WebhookSignatureValidatorInterface;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander
     */
    public function createMollieCachedOptionsExpander(): MollieCachedOptionsExpander
    {
        return new MollieCachedOptionsExpander(
            $this->getMollieApiClient(),
            $this->createMollieMapper(),
            $this->getLocaleClient(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Yves\Mollie\Mapper\MollieMapperInterface
     */
    public function createMollieMapper(): MollieMapperInterface
    {
        return new MollieMapper();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieCreditCardSubForm(): SubFormInterface
    {
        return new MollieCreditCardSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMolliePayPalSubForm(): SubFormInterface
    {
        return new MolliePayPalSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieBankTransferSubForm(): SubFormInterface
    {
        return new MollieBankTransferSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieKlarnaSubForm(): SubFormInterface
    {
        return new MollieKlarnaSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieKlarnaPayLaterSubForm(): SubFormInterface
    {
        return new MollieKlarnaPayLaterSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieKlarnaPayNowSubForm(): SubFormInterface
    {
        return new MollieKlarnaPayNowSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieKlarnaSliceItSubForm(): SubFormInterface
    {
        return new MollieKlarnaSliceItSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieEpsSubForm(): SubFormInterface
    {
        return new MollieEpsSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieIdealSubForm(): SubFormInterface
    {
        return new MollieIdealSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieBancontactSubForm(): SubFormInterface
    {
        return new MollieBancontactSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieKbcSubForm(): SubFormInterface
    {
        return new MollieKbcSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMolliePayByBankSubForm(): SubFormInterface
    {
        return new MolliePayByBankSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieTrustlySubForm(): SubFormInterface
    {
        return new MollieTrustlySubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieTrustlySubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieTrustlySubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieMbWaySubForm(): SubFormInterface
    {
        return new MollieMbWaySubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieMbWaySubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieMbWaySubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieSwishSubForm(): SubFormInterface
    {
        return new MollieSwishSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieSwishSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieSwishSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieSatispaySubForm(): SubFormInterface
    {
        return new MollieSatispaySubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieSatispaySubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieSatispaySubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieTwintSubForm(): SubFormInterface
    {
        return new MollieTwintSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieTwintSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieTwintSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieVippsSubForm(): SubFormInterface
    {
        return new MollieVippsSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieVippsSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieVippsSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieApplePaySubForm(): SubFormInterface
    {
        return new MollieApplePaySubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieCreditCardSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieCreditCardSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMolliePayPalSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MolliePayPalSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieBankTransferSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieBankTransferSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaPayLaterSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaPayLaterSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaPayNowSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaPayNowSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaSliceItSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaSliceItSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieEpsSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieEpsSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieIdealSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieIdealSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieBancontactSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieBancontactSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKbcSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKbcSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMolliePayByBankSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MolliePayByBankSubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieApplePaySubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieApplePaySubFormDataProvider(
            $this->createMollieCachedOptionsExpander(),
        );
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieCreditCardPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentCreditCardHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMolliePayPalPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentPayPalHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieBankTransferPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentBankTransferHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPayLaterPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaPayLaterHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPayNowPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaPayNowHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaSliceItPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaSliceItHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieEpsPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentEpsHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieIdealPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentIdealHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieBancontactPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentBancontactHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieKbcPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKbcHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMolliePayByBankPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentPayByBankHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
     */
    public function createMollieApplePayPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentApplePayHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMollieMbWayPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentMbWayHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMolliePaymentSatispayHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentSatispayHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMolliePaymentSwishHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentSwishHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMolliePaymentTrustlyHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentTrustlyHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMolliePaymentTwintHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentTwintHandler();
    }

     /**
      * @return \Mollie\Yves\Mollie\Handler\Payment\MolliePaymentHandlerInterface
      */
    public function createMolliePaymentVippsHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentVippsHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Validator\WebhookSignatureValidatorInterface
     */
    public function createWebhookSignatureValidator(): WebhookSignatureValidatorInterface
    {
        return new WebhookSignatureValidator(
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function getMollieApiClient(): MollieClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_MOLLIE);
    }

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface
     */
    public function getQuoteClient(): MollieToQuoteClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return array<\Mollie\Yves\Mollie\Plugin\Webhook\MollieWebhookHandlerPluginInterface>
     */
    public function getMollieWebhookHandlerPlugins(): array
    {
        return $this->getProvidedDependency(MollieDependencyProvider::PLUGINS_MOLLIE_WEBHOOK_HANDLER);
    }

    /**
     * @return array<\Mollie\Yves\Mollie\Plugin\Webhook\MollieNextGenWebhookHandlerPluginInterface>
     */
    public function getMollieNextGenWebhookHandlerPlugins(): array
    {
        return $this->getProvidedDependency(MollieDependencyProvider::PLUGINS_MOLLIE_NEXT_GEN_WEBHOOK_HANDLER);
    }

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface
     */
    public function getLocaleClient(): MollieToLocaleClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_LOCALE);
    }
}

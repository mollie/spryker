<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Yves\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Yves\Mollie\Handler\MolliePaymentBancontactHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentBankTransferHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentCreditCardHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentEpsHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface;
use Mollie\Yves\Mollie\Handler\MolliePaymentIdealHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentKbcHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentKlarnaHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentKlarnaPayLaterHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentKlarnaPayNowHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentKlarnaSliceItHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentPayByBankHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentPayPalHandler;
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
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MolliePayByBankSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MolliePayPalSubFormDataProvider;
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
use Mollie\Yves\Mollie\PaymentPage\Form\MolliePayByBankSubForm;
use Mollie\Yves\Mollie\PaymentPage\Form\MolliePayPalSubForm;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;

/**
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
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
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieCreditCardSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieCreditCardSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMolliePayPalSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MolliePayPalSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieBankTransferSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieBankTransferSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaPayLaterSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaPayLaterSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaPayNowSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaPayNowSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKlarnaSliceItSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKlarnaSliceItSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieEpsSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieEpsSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieIdealSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieIdealSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieBancontactSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieBancontactSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieKbcSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieKbcSubFormDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMolliePayByBankSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MolliePayByBankSubFormDataProvider();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieCreditCardPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentCreditCardHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMolliePayPalPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentPayPalHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieBankTransferPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentBankTransferHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPayLaterPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaPayLaterHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaPayNowPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaPayNowHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieKlarnaSliceItPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKlarnaSliceItHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieEpsPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentEpsHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieIdealPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentIdealHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieBancontactPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentBancontactHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMollieKbcPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentKbcHandler();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentHandlerInterface
     */
    public function createMolliePayByBankPaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentPayByBankHandler();
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
}

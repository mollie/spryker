<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidator;
use Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidatorInterface;
use Mollie\Zed\Mollie\Communication\DataProvider\MolliePaymentMethodsDataProvider;
use Mollie\Zed\Mollie\Communication\Form\CreatePaymentLinkForm;
use Mollie\Zed\Mollie\Communication\Form\DataProvider\PaymentLinkFormDataProvider;
use Mollie\Zed\Mollie\Communication\Form\PaymentMethodConfigForm;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapper;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Communication\Mapper\Order\OrderItemMapper;
use Mollie\Zed\Mollie\Communication\Mapper\Order\OrderItemMapperInterface;
use Mollie\Zed\Mollie\Communication\Mapper\PaymentLink\MolliePaymentLinkMapper;
use Mollie\Zed\Mollie\Communication\Mapper\PaymentLink\MolliePaymentLinkMapperInterface;
use Mollie\Zed\Mollie\Communication\Table\MolliePaymentLinkTable;
use Mollie\Zed\Mollie\Communication\Table\MolliePaymentMethodsTable;
use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToSalesFacadeInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacade getFacade();
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\MolliePaymentMethodsTable
     */
    public function createMolliePaymentMethodsTable(): MolliePaymentMethodsTable
    {
        return new MolliePaymentMethodsTable(
            $this->createMolliePaymentMethodsDataProvider(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\DataProvider\MolliePaymentMethodsDataProvider
     */
    public function createMolliePaymentMethodsDataProvider(): MolliePaymentMethodsDataProvider
    {
        return new MolliePaymentMethodsDataProvider(
            $this->createMollieCommunicationMapper(),
            $this->getMollieClient(),
            $this->getLocaleFacade(),
            $this->getFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidatorInterface
     */
    public function createMollieCacheInvalidator(): MollieCacheInvalidatorInterface
    {
        return new MollieCacheInvalidator(
            $this->createMollieCommunicationMapper(),
            $this->getMollieClient(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\MolliePaymentLinkTable
     */
    public function createPaymentLinkTable(): MolliePaymentLinkTable
    {
        return new MolliePaymentLinkTable(
            $this->getMolliePaymentLinkPropelQuery(),
            $this->createMolliePaymentLinkDataProvider(),
            $this->getMollieService(),
        );
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createPaymentLinkForm(array $data = [], array $options = []): FormInterface
    {
        return $this->getFormFactory()
            ->create(
                CreatePaymentLinkForm::class,
                $data,
                $options,
            );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider
     */
    public function createMolliePaymentLinkDataProvider(): MolliePaymentLinkDataProvider
    {
        return new MolliePaymentLinkDataProvider($this->getMollieClient());
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Form\DataProvider\PaymentLinkFormDataProvider
     */
    public function createMolliePaymentLinkFormDataProvider(): PaymentLinkFormDataProvider
    {
        return new PaymentLinkFormDataProvider(
            $this->getCurrencyFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface
     */
    public function createMollieCommunicationMapper(): MollieCommunicationMapperInterface
    {
        return new MollieCommunicationMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Mapper\PaymentLink\MolliePaymentLinkMapperInterface
     */
    public function createPaymentLinkMapper(): MolliePaymentLinkMapperInterface
    {
        return new MolliePaymentLinkMapper(
            $this->getMollieService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Mapper\Order\OrderItemMapperInterface
     */
    public function createOrderItemMapper(): OrderItemMapperInterface
    {
        return new OrderItemMapper();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createPaymentMethodConfigForm(array $data = [], array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(PaymentMethodConfigForm::class, $data, $options);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function getMollieClient(): MollieClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_MOLLIE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface
     */
    public function getLocaleFacade(): MollieToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface
     */
    public function getMailFacade(): MollieToMailFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToSalesFacadeInterface
     */
    public function getSalesFacade(): MollieToSalesFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery
     */
    public function getMolliePaymentLinkPropelQuery(): SpyMolliePaymentLinkQuery
    {
        return $this->getProvidedDependency(MollieDependencyProvider::PROPEL_QUERY_PAYMENT_LINK);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface
     */
    public function getCurrencyFacade(): MollieToCurrencyFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_CURRENCY);
    }

    /**
     * @return \Mollie\Service\Mollie\MollieServiceInterface
     */
    public function getMollieService(): MollieServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_MOLLIE);
    }
}

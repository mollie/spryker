<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculator;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigReader;
use Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigReaderInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigWriter;
use Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigWriterInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\AddressExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\PaymentExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ShipmentMethodExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutOrderPlacer;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutOrderPlacerInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparer;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparerInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunder;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping\ExpressCheckoutShippingOptionsProvider;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping\ExpressCheckoutShippingOptionsProviderInterface;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilter;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilterInterface;
use Mollie\Zed\Mollie\Business\Filter\MollieRefundFilter;
use Mollie\Zed\Mollie\Business\Filter\MollieRefundFilterInterface;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandler;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\Handler\MollieExpirationWarningHandler;
use Mollie\Zed\Mollie\Business\Handler\MollieExpirationWarningHandlerInterface;
use Mollie\Zed\Mollie\Business\Handler\MollieMailHandler;
use Mollie\Zed\Mollie\Business\Handler\MollieMailHandlerInterface;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandler;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentLinkHandler;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentLinkHandlerInterface;
use Mollie\Zed\Mollie\Business\Mapper\Capture\CaptureMapper;
use Mollie\Zed\Mollie\Business\Mapper\Capture\CaptureMapperInterface;
use Mollie\Zed\Mollie\Business\Mapper\Order\OrderMapper;
use Mollie\Zed\Mollie\Business\Mapper\Order\OrderMapperInterface;
use Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapper;
use Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface;
use Mollie\Zed\Mollie\Business\Order\OrderUpdater;
use Mollie\Zed\Mollie\Business\Order\OrderUpdaterInterface;
use Mollie\Zed\Mollie\Business\Payment\RequestSender\MolliePaymentCaptureRequestSender;
use Mollie\Zed\Mollie\Business\Payment\RequestSender\MolliePaymentCaptureRequestSenderInterface;
use Mollie\Zed\Mollie\Business\Payment\RequestSender\MollieReleaseAuthorizationRequestSender;
use Mollie\Zed\Mollie\Business\Payment\Status\MolliePaymentStatusHandler;
use Mollie\Zed\Mollie\Business\Payment\Status\MolliePaymentStatusHandlerInterface;
use Mollie\Zed\Mollie\Business\Processor\Capture\CaptureProcessor;
use Mollie\Zed\Mollie\Business\Processor\Capture\CaptureProcessorInterface;
use Mollie\Zed\Mollie\Business\Processor\PaymentLink\PaymentLinkProcessor;
use Mollie\Zed\Mollie\Business\Processor\PaymentLink\PaymentLinkProcessorInterface;
use Mollie\Zed\Mollie\Business\Processor\Refund\RefundProcessor;
use Mollie\Zed\Mollie\Business\Processor\Refund\RefundProcessorInterface;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriter;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCalculationFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCheckoutFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 * @method \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface getEntityManager()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Business\Order\OrderUpdaterInterface
     */
    public function createPaymentStatusUpdater(): OrderUpdaterInterface
    {
        return new OrderUpdater(
            $this->getEntityManager(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface
     */
    public function createOrderItemGrossAmountCalculator(): OrderItemGrossAmountCalculatorInterface
    {
        return new OrderItemGrossAmountCalculator();
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Processor\Refund\RefundProcessorInterface
     */
    public function createRefundProcessor(): RefundProcessorInterface
    {
        return new RefundProcessor(
            $this->createOrderItemGrossAmountCalculator(),
            $this->getRepository(),
            $this->getMollieClient(),
            $this->getEntityManager(),
            $this->createMollieRefundMapper(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Processor\Capture\CaptureProcessorInterface
     */
    public function createCaptureProcessor(): CaptureProcessorInterface
    {
        return new CaptureProcessor(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createCaptureMapper(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Mapper\Order\OrderMapperInterface
     */
    public function createOrderMapper(): OrderMapperInterface
    {
        return new OrderMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface
     */
    public function createMollieRefundMapper(): MollieRefundMapperInterface
    {
        return new MollieRefundMapper(
            $this->createMollieRefundFilter(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Filter\MollieRefundFilterInterface
     */
    public function createMollieRefundFilter(): MollieRefundFilterInterface
    {
        return new MollieRefundFilter();
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Payment\RequestSender\MolliePaymentCaptureRequestSenderInterface
     */
    public function createMolliePaymentCaptureRequestSender(): MolliePaymentCaptureRequestSenderInterface
    {
        return new MolliePaymentCaptureRequestSender(
            $this->getMollieClient(),
            $this->getMollieService(),
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createCaptureMapper(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Payment\RequestSender\MollieReleaseAuthorizationRequestSender
     */
    public function createMollieReleaseAuthorizationRequestSender(): MollieReleaseAuthorizationRequestSender
    {
        return new MollieReleaseAuthorizationRequestSender(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->getMollieClient(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Payment\Status\MolliePaymentStatusHandlerInterface
     */
    public function createMolliePaymentStatusHandler(): MolliePaymentStatusHandlerInterface
    {
        return new MolliePaymentStatusHandler(
            $this->getRepository(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Mapper\Capture\CaptureMapperInterface
     */
    public function createCaptureMapper(): CaptureMapperInterface
    {
        return new CaptureMapper(
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface
     */
    public function getOmsFacade(): MollieToOmsInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::LOGGER);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function getMollieClient(): MollieClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_MOLLIE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface
     */
    public function createMolliePaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentHandler(
            $this->getMollieClient(),
            $this->getStorageClient(),
            $this->createMolliePaymentWriter(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface
     */
    public function createMolliePaymentWriter(): MolliePaymentWriterInterface
    {
        return new MolliePaymentWriter($this->getEntityManager());
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilterInterface
     */
    public function createMolliePaymentMethodsFilter(): MolliePaymentMethodsFilterInterface
    {
        return new MolliePaymentMethodsFilter(
            $this->getMollieClient(),
            $this->getMollieService(),
            $this->getLocaleFacade(),
            $this->getRepository(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Service\Mollie\MollieServiceInterface
     */
    public function getMollieService(): MollieServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_MOLLIE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface
     */
    public function getLocaleFacade(): MollieToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\MollieMailHandlerInterface
     */
    public function createMailHandler(): MollieMailHandlerInterface
    {
        return new MollieMailHandler(
            $this->getLocaleFacade(),
            $this->getMailFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\MollieExpirationWarningHandlerInterface
     */
    public function createMollieExpirationWarningHandler(): MollieExpirationWarningHandlerInterface
    {
        return new MollieExpirationWarningHandler(
            $this->getConfig(),
            $this->getRepository(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface
     */
    public function getMailFacade(): MollieToMailFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentLinkHandler
     */
    public function createMolliePaymentLinkHandler(): MolliePaymentLinkHandlerInterface
    {
        return new MolliePaymentLinkHandler(
            $this->getMollieClient(),
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Processor\PaymentLink\PaymentLinkProcessorInterface
     */
    public function createPaymentLinkProcessor(): PaymentLinkProcessorInterface
    {
        return new PaymentLinkProcessor(
            $this->getMollieService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigReaderInterface
     */
    public function createExpressCheckoutConfigReader(): ExpressCheckoutConfigReaderInterface
    {
        return new ExpressCheckoutConfigReader(
            $this->getConfig(),
            $this->getRepository(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigWriterInterface
     */
    public function createExpressCheckoutConfigWriter(): ExpressCheckoutConfigWriterInterface
    {
        return new ExpressCheckoutConfigWriter(
            $this->getEntityManager(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface
     */
    public function createExpressCheckoutMolliePaymentHandler(): ExpressCheckoutMolliePaymentHandlerInterface
    {
        return new ExpressCheckoutMolliePaymentHandler(
            $this->getEntityManager(),
            $this->getStorageClient(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutOrderPlacerInterface
     */
    public function createExpressCheckoutOrderPlacer(): ExpressCheckoutOrderPlacerInterface
    {
        return new ExpressCheckoutOrderPlacer(
            $this->createExpressCheckoutQuotePreparer(),
            $this->getCheckoutFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparerInterface
     */
    public function createExpressCheckoutQuotePreparer(): ExpressCheckoutQuotePreparerInterface
    {
        return new ExpressCheckoutQuotePreparer($this->getExpressCheckoutOrderQuoteExpanders(), $this->getCalculationFacade());
    }

    /**
     * @return array<\Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface>
     */
    public function getExpressCheckoutOrderQuoteExpanders(): array
    {
        return [
            $this->createExpressCheckoutAddressExpander(),
            $this->createExpressCheckoutShipmentMethodExpander(),
            $this->createExpressCheckoutPaymentExpander(),
        ];
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface
     */
    public function createExpressCheckoutAddressExpander(): ExpressCheckoutQuoteExpanderInterface
    {
        return new AddressExpander();
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface
     */
    public function createExpressCheckoutShipmentMethodExpander(): ExpressCheckoutQuoteExpanderInterface
    {
        return new ShipmentMethodExpander($this->getShipmentFacade());
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface
     */
    public function createExpressCheckoutPaymentExpander(): ExpressCheckoutQuoteExpanderInterface
    {
        return new PaymentExpander();
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToCheckoutFacadeInterface
     */
    public function getCheckoutFacade(): MollieToCheckoutFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_CHECKOUT);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToCalculationFacadeInterface
     */
    public function getCalculationFacade(): MollieToCalculationFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_CALCULATION);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface
     */
    public function getShipmentFacade(): MollieToShipmentFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_SHIPMENT);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping\ExpressCheckoutShippingOptionsProviderInterface
     */
    public function createExpressCheckoutShippingOptionsProvider(): ExpressCheckoutShippingOptionsProviderInterface
    {
        return new ExpressCheckoutShippingOptionsProvider(
            $this->getShipmentFacade(),
            $this->getMollieService(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface
     */
    public function createExpressCheckoutPaymentRefunder(): ExpressCheckoutPaymentRefunderInterface
    {
        return new ExpressCheckoutPaymentRefunder(
            $this->getMollieClient(),
            $this->getStorageClient(),
            $this->getConfig(),
        );
    }
}

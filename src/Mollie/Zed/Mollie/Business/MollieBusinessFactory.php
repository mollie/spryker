<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculator;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilter;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilterInterface;
use Mollie\Zed\Mollie\Business\Filter\MollieRefundFilter;
use Mollie\Zed\Mollie\Business\Filter\MollieRefundFilterInterface;
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
use Mollie\Zed\Mollie\Business\Payment\Status\MolliePaymentStatusHandler;
use Mollie\Zed\Mollie\Business\Payment\Status\MolliePaymentStatusHandlerInterface;
use Mollie\Zed\Mollie\Business\Processor\Capture\CaptureProcessor;
use Mollie\Zed\Mollie\Business\Processor\Capture\CaptureProcessorInterface;
use Mollie\Zed\Mollie\Business\Processor\Refund\RefundProcessor;
use Mollie\Zed\Mollie\Business\Processor\Refund\RefundProcessorInterface;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriter;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
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
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToMailFacadeInterface
     */
    public function getMailFacade(): MollieToMailFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return MolliePaymentLinkHandler
     */
    public function createMolliePaymentLinkHandler(): MolliePaymentLinkHandlerInterface
    {
        return new MolliePaymentLinkHandler($this->getMollieClient());
    }
}

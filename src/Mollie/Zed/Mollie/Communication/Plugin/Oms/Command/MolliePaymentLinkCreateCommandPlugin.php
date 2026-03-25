<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\OmsEventTriggerResponseTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class MolliePaymentLinkCreateCommandPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * @param array<mixed> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<mixed>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->findOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        $molliePaymentLinkTransfer = $this->getFacade()->processPaymentLinkData($orderTransfer);
        $molliePaymentLinkApiResponseTransfer = $this->getFacade()->createPaymentLink($molliePaymentLinkTransfer);

        $omsEventTriggerResponseTransfer = $this->createOmsEventTriggerResponseTransfer($molliePaymentLinkApiResponseTransfer);

        return ['oms_event_trigger_response' => $omsEventTriggerResponseTransfer];
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer $molliePaymentLinkApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\OmsEventTriggerResponseTransfer
     */
    protected function createOmsEventTriggerResponseTransfer(
        MolliePaymentLinkApiResponseTransfer $molliePaymentLinkApiResponseTransfer,
    ): OmsEventTriggerResponseTransfer {
        $omsEventTriggerResponseTransfer = new OmsEventTriggerResponseTransfer();
        $omsEventTriggerResponseTransfer->setIsSuccessful($molliePaymentLinkApiResponseTransfer->getIsSuccessful());

        if (!$molliePaymentLinkApiResponseTransfer->getIsSuccessful()) {
            $messageTrasfer = new MessageTransfer();
            $messageTrasfer->setValue($molliePaymentLinkApiResponseTransfer->getMessage());
            $omsEventTriggerResponseTransfer->addMessage($messageTrasfer);
        }

        return $omsEventTriggerResponseTransfer;
    }
}

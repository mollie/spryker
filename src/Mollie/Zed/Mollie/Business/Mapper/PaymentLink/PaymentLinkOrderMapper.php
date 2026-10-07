<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Mapper\PaymentLink;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Shipment\ShipmentConfig;

class PaymentLinkOrderMapper implements PaymentLinkOrderMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\MollieLinesTransfer>
     */
    public function mapOrderItemsAndExpensesToMollieLines(OrderTransfer $orderTransfer): ArrayObject
    {
        $mollieLinesTransfers = new ArrayObject();

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $itemMollieLinesTransfer = $this->mapItemToMollieLine($itemTransfer, $orderTransfer->getCurrency()->getCode());
            $mollieLinesTransfers->append($itemMollieLinesTransfer);
        }

        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            $expenseMollieLinesTransfer = $this->mapExpenseToMollieLine($expenseTransfer, $orderTransfer->getCurrency()->getCode());
            $mollieLinesTransfers->append($expenseMollieLinesTransfer);
        }

        return $mollieLinesTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAddressTransfer
     */
    public function mapOrderBillingAddressToMollieAddress(OrderTransfer $orderTransfer): MollieAddressTransfer
    {
        $billingAddressTransfer = $orderTransfer->getBillingAddress();

        $streetAndNumber = trim($billingAddressTransfer->getAddress1() . ' ' . $billingAddressTransfer->getAddress2());
        $email = $this->getEmailForBillingAddress($billingAddressTransfer, $orderTransfer);

        $mollieAddressTransfer = new MollieAddressTransfer();
        $mollieAddressTransfer
            ->setTitle($billingAddressTransfer->getSalutation())
            ->setGivenName($billingAddressTransfer->getFirstName())
            ->setFamilyName($billingAddressTransfer->getLastName())
            ->setOrganizationName($billingAddressTransfer->getCompany())
            ->setStreetAndNumber($streetAndNumber)
            ->setStreetAdditional($billingAddressTransfer->getAddress3())
            ->setPostalCode($billingAddressTransfer->getZipCode())
            ->setEmail($email)
            ->setPhone($billingAddressTransfer->getPhone())
            ->setCity($billingAddressTransfer->getCity())
            ->setCountry($billingAddressTransfer->getIso2Code());

        return $mollieAddressTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param string $currencyCode
     *
     * @return \Generated\Shared\Transfer\MollieLinesTransfer
     */
    protected function mapItemToMollieLine(ItemTransfer $itemTransfer, string $currencyCode): MollieLinesTransfer
    {
        $unitPrice = $this->mollieService->convertIntegerToMollieAmount($itemTransfer->getUnitPriceToPayAggregation(), $currencyCode);
        $totalAmount = $this->mollieService->convertIntegerToMollieAmount($itemTransfer->getSumPriceToPayAggregation(), $currencyCode);
        $vatAmount = $this->mollieService->convertIntegerToMollieAmount($itemTransfer->getSumTaxAmountFullAggregation(), $currencyCode);
        $vatRate = $this->formatVatRate((float)$itemTransfer->getTaxRate());

        $mollieLinesTransfer = new MollieLinesTransfer();
        $mollieLinesTransfer
            ->setType(MollieConstants::PRODUCT_TYPE_PHYSICAL)
            ->setDescription($itemTransfer->getName())
            ->setSku($itemTransfer->getSku())
            ->setQuantity($itemTransfer->getQuantity())
            ->setUnitPrice($unitPrice)
            ->setTotalAmount($totalAmount)
            ->setVatRate($vatRate)
            ->setVatAmount($vatAmount);

        return $mollieLinesTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param string $currencyCode
     *
     * @return \Generated\Shared\Transfer\MollieLinesTransfer
     */
    protected function mapExpenseToMollieLine(ExpenseTransfer $expenseTransfer, string $currencyCode): MollieLinesTransfer
    {
        $unitPrice = $this->mollieService->convertIntegerToMollieAmount($expenseTransfer->getUnitPriceToPayAggregation(), $currencyCode);
        $totalAmount = $this->mollieService->convertIntegerToMollieAmount($expenseTransfer->getSumPriceToPayAggregation(), $currencyCode);
        $vatAmount = $this->mollieService->convertIntegerToMollieAmount($expenseTransfer->getSumTaxAmount(), $currencyCode);
        $vatRate = $this->formatVatRate((float)$expenseTransfer->getTaxRate());
        $lineType = $this->getMollieLineTypeForExpense($expenseTransfer);

        $mollieLinesTransfer = new MollieLinesTransfer();
        $mollieLinesTransfer
            ->setType($lineType)
            ->setDescription($expenseTransfer->getName())
            ->setQuantity($expenseTransfer->getQuantity())
            ->setUnitPrice($unitPrice)
            ->setTotalAmount($totalAmount)
            ->setVatRate($vatRate)
            ->setVatAmount($vatAmount);

        return $mollieLinesTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return string
     */
    protected function getMollieLineTypeForExpense(ExpenseTransfer $expenseTransfer): string
    {
        $expenseType = $expenseTransfer->getType();

        if ($expenseType === ShipmentConfig::SHIPMENT_EXPENSE_TYPE) {
            return MollieConstants::PRODUCT_TYPE_SHIPPING_FEE;
        }

        return MollieConstants::PRODUCT_TYPE_SURCHARGE;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $billingAddressTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string|null
     */
    protected function getEmailForBillingAddress(AddressTransfer $billingAddressTransfer, OrderTransfer $orderTransfer): ?string
    {
        $billingAddressEmail = $billingAddressTransfer->getEmail();

        if ($billingAddressEmail) {
            return $billingAddressEmail;
        }

        $orderEmail = $orderTransfer->getEmail();

        return $orderEmail;
    }

    /**
     * @param float $taxRate
     *
     * @return string
     */
    protected function formatVatRate(float $taxRate): string
    {
        $vatRate = number_format($taxRate, 2, '.', '');

        return $vatRate;
    }
}

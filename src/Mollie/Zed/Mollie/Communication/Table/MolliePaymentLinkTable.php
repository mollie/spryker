<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Table;

use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class MolliePaymentLinkTable extends AbstractTable
{
    /**
     * @var string
     */
    protected const COL_ID = 'id_mollie_payment_link';

    /**
     * @var string
     */
    protected const COL_MOLLIE_ID = 'id';

    /**
     * @var string
     */
    protected const COL_ID_SALES_ORDER = 'id_sales_order';

    /**
     * @var string
     */
    protected const COL_TYPE = 'type';

    /**
     * @var string
     */
    protected const COL_SEQUENCE_TYPE = 'sequence_type';

    /**
     * @var string
     */
    protected const COL_AMOUNT = 'amount';

    /**
     * @var string
     */
    protected const COL_CURRENCY = 'currency';

    /**
     * @var string
     */
    protected const COL_DESCRIPTION = 'description';

    /**
     * @var string
     */
    protected const COL_PAYMENT_LINK = 'payment_link';

    /**
     * @var string
     */
    protected const COL_EXPIRY_DATE = 'expiry_date';

    /**
     * @var string
     */
    protected const COL_STATUS = 'status';

    /**
     * @var string
     */
    protected const COL_PAYMENT_METHODS = 'payment_methods';

    /**
     * @var string
     */
    protected const COL_CREATED_AT = 'created_at';

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery $paymentLinkQuery
     * @param \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider $dataProvider
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected SpyMolliePaymentLinkQuery $paymentLinkQuery,
        protected MolliePaymentLinkDataProvider $dataProvider,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config->setHeader([
            static::COL_ID => 'ID',
            static::COL_MOLLIE_ID => 'Mollie ID',
            static::COL_ID_SALES_ORDER => 'ID Order',
            static::COL_DESCRIPTION => 'Description',
            static::COL_TYPE => 'Type',
            static::COL_SEQUENCE_TYPE => 'Sequence Type',
            static::COL_AMOUNT => 'Amount',
            static::COL_CURRENCY => 'Currency',
            static::COL_STATUS => 'Status',
            static::COL_PAYMENT_LINK => 'Payment Link',
            static::COL_EXPIRY_DATE => 'Expiry Date',
            static::COL_PAYMENT_METHODS => 'Payment Methods',
            static::COL_CREATED_AT => 'Created',
        ]);

        $config->setSearchable([
            static::COL_DESCRIPTION,
            static::COL_ID,
            static::COL_MOLLIE_ID,
            static::COL_ID_SALES_ORDER,
        ]);

        $config->setSortable([
            static::COL_ID,
            static::COL_ID_SALES_ORDER,
            static::COL_DESCRIPTION,
            static::COL_AMOUNT,
            static::COL_PAYMENT_LINK,
            static::COL_CREATED_AT,
            static::COL_EXPIRY_DATE,
            static::COL_STATUS,
        ]);

        $config->setDefaultSortField(
            static::COL_ID,
            TableConfiguration::SORT_DESC,
        );

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array<mixed>
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $queryResults = $this->runQuery($this->paymentLinkQuery, $config, true);

        $results = [];

        /**
         * @var \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $paymentLink
         */
        foreach ($queryResults->getData() as $paymentLink) {
            $amount = $this->mollieService->convertIntegerToDecimal($paymentLink->getAmount());

            $results[] = [
                static::COL_ID => $paymentLink->getIdMolliePaymentLink(),
                static::COL_MOLLIE_ID => $paymentLink->getId(),
                static::COL_ID_SALES_ORDER => $paymentLink->getFkSalesOrder(),
                static::COL_DESCRIPTION => $paymentLink->getDescription(),
                static::COL_TYPE => $paymentLink->getType(),
                static::COL_SEQUENCE_TYPE => $paymentLink->getSequenceType(),
                static::COL_AMOUNT => $amount,
                static::COL_CURRENCY => $paymentLink->getCurrency(),
                static::COL_STATUS => $paymentLink->getStatus(),
                static::COL_PAYMENT_LINK => $paymentLink->getPaymentLinkUrl(),
                static::COL_PAYMENT_METHODS => $paymentLink->getPaymentMethods(),
                static::COL_EXPIRY_DATE => $paymentLink->getExpiryDate('Y-m-d H:i:s') ?: '-',
                static::COL_CREATED_AT => $paymentLink->getCreatedAt('Y-m-d H:i:s'),
            ];
        }

        return $results;
    }
}

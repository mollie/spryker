<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Table;

use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider;
use Orm\Zed\Mollie\Persistence\Map\SpyMolliePaymentLinkTableMap;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;


class MolliePaymentLinkTable extends AbstractTable
{
    protected const COL_ID = SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK;
    protected const COL_TYPE = SpyMolliePaymentLinkTableMap::COL_TYPE;
    protected const COL_AMOUNT = SpyMolliePaymentLinkTableMap::COL_AMOUNT;
    protected const COL_CURRENCY = SpyMolliePaymentLinkTableMap::COL_CURRENCY;
    protected const COL_DESCRIPTION = SpyMolliePaymentLinkTableMap::COL_DESCRIPTION;
    protected const COL_STATUS = SpyMolliePaymentLinkTableMap::COL_STATUS;
    protected const COL_EXPIRY_DATE = SpyMolliePaymentLinkTableMap::COL_EXPIRY_DATE;
    protected const COL_CREATED_AT = SpyMolliePaymentLinkTableMap::COL_CREATED_AT;
    protected const COL_ACTIONS = 'actions';

    /**
     * @param MolliePaymentLinkDataProvider $dataProvider
     */
    public function __construct(
        //protected SpyMolliePaymentLinkQuery $paymentLinkQuery,
        protected MolliePaymentLinkDataProvider $dataProvider,
    )
    {
    }

    /**
     * @param TableConfiguration $config
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config->setHeader([
            static::COL_ID => 'ID',
            static::COL_DESCRIPTION => 'Description',
            static::COL_TYPE => 'Type',
            static::COL_AMOUNT => 'Amount',
            static::COL_CURRENCY => 'Currency',
            static::COL_STATUS => 'Status',
            static::COL_EXPIRY_DATE => 'Expiry Date',
            static::COL_CREATED_AT => 'Created',
            static::COL_ACTIONS => 'Actions',
        ]);

        $config->setSearchable([
            static::COL_DESCRIPTION,
            static::COL_ID,
        ]);

        $config->setSortable([
            static::COL_ID,
            static::COL_DESCRIPTION,
            static::COL_TYPE,
            static::COL_AMOUNT,
            static::COL_STATUS,
            static::COL_CREATED_AT,
            static::COL_EXPIRY_DATE,
        ]);

        $config->setDefaultSortField(
            static::COL_ID,
            TableConfiguration::SORT_DESC
        );

        $config->setRawColumns([
            static::COL_ACTIONS,
            static::COL_STATUS,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
//        $queryResults = $this->runQuery($this->paymentLinkQuery, $config);
        $paymentLinks = $this->dataProvider->getData();
        $results = [];

        foreach ($queryResults as $paymentLink) {
            $results[] = [
                static::COL_ID => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
                static::COL_DESCRIPTION => $paymentLink[SpyMolliePaymentLinkTableMap::COL_DESCRIPTION],
                static::COL_TYPE => ucfirst($paymentLink[SpyMolliePaymentLinkTableMap::COL_TYPE]),
                static::COL_AMOUNT => number_format((float)$paymentLink[SpyMolliePaymentLinkTableMap::COL_AMOUNT], 2),
                static::COL_CURRENCY => $paymentLink[SpyMolliePaymentLinkTableMap::COL_CURRENCY],
                static::COL_STATUS => $this->generateStatusLabel($paymentLink[SpyMolliePaymentLinkTableMap::COL_STATUS]),
                static::COL_EXPIRY_DATE => $paymentLink[SpyMolliePaymentLinkTableMap::COL_EXPIRY_DATE] ?: '-',
                static::COL_CREATED_AT => $paymentLink[SpyMolliePaymentLinkTableMap::COL_CREATED_AT],
                static::COL_ACTIONS => $this->buildLinks($paymentLink),
            ];
        }

        return $results;
    }

    /**
     * @param array $paymentLink
     *
     * @return string
     */
    protected function buildLinks(array $paymentLink): string
    {
        $buttons = [];

        $buttons[] = $this->generateViewButton(
            Url::generate('/mollie/payment-link/view', [
                'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
            ]),
            'View'
        );

        $buttons[] = $this->generateEditButton(
            Url::generate('/mollie/payment-link/edit', [
                'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
            ]),
            'Edit'
        );

        if ($paymentLink[SpyMolliePaymentLinkTableMap::COL_STATUS] !== 'paid') {
            $buttons[] = $this->generateRemoveButton(
                Url::generate('/mollie/payment-link/delete', [
                    'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
                ]),
                'Delete'
            );
        }

        return implode(' ', $buttons);
    }

    /**
     * @param string $status
     *
     * @return string
     */
    protected function generateStatusLabel(string $status): string
    {
        $labelClass = match($status) {
            'draft' => 'label-default',
            'active' => 'label-success',
            'expired' => 'label-warning',
            'paid' => 'label-info',
            default => 'label-default',
        };

        return sprintf(
            '<span class="label %s">%s</span>',
            $labelClass,
            ucfirst($status)
        );
    }
}

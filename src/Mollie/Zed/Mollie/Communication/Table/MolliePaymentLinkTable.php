<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Table;

use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider;
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
    protected const COL_TYPE = 'type';

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
    protected const COL_CREATED_AT = 'created_at';

    /**
     * @var string
     */
    protected const COL_ACTIONS = 'actions';

    /**
     * @param \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentLinkDataProvider $dataProvider
     */
    public function __construct(
        //protected SpyMolliePaymentLinkQuery $paymentLinkQuery,
        protected MolliePaymentLinkDataProvider $dataProvider,
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
            static::COL_DESCRIPTION => 'Description',
            static::COL_TYPE => 'Type',
            static::COL_AMOUNT => 'Amount',
            static::COL_CURRENCY => 'Currency',
            static::COL_PAYMENT_LINK => 'Payment Link',
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
            static::COL_PAYMENT_LINK,
            static::COL_CREATED_AT,
            static::COL_EXPIRY_DATE,
        ]);

        $config->setDefaultSortField(
            static::COL_ID,
            TableConfiguration::SORT_DESC,
        );

        $config->setRawColumns([
            static::COL_ACTIONS,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        //$queryResults = $this->runQuery($this->paymentLinkQuery, $config);
        $molliePaymentLinkApiResponseTransfer = $this->dataProvider->getData();

        if (!$molliePaymentLinkApiResponseTransfer) {
            return [];
        }

        $results = [];

        foreach ($molliePaymentLinkApiResponseTransfer->getMolliePaymentLinks()?->getPaymentLinks() as $paymentLink) {
            $results[] = [
                static::COL_ID => $paymentLink->getId(),
                static::COL_DESCRIPTION => $paymentLink->getDescription(),
                static::COL_TYPE => $paymentLink->getType(),
                static::COL_AMOUNT => number_format((float)$paymentLink->getAmount()?->getValue(), 2),
                static::COL_CURRENCY => $paymentLink->getAmount()?->getCurrency(),
                static::COL_PAYMENT_LINK => $paymentLink->getLinks()->getPaymentLink()->getHref(),
                static::COL_EXPIRY_DATE => $paymentLink->getExpiresAt() ?: '-',
                static::COL_CREATED_AT => $paymentLink->getCreatedAt(),
                static::COL_ACTIONS => $this->buildLinks(),
            ];
        }

        return $results;
    }

    /**
     * @return string
     */
    protected function buildLinks(): string
    {
        $buttons = [];

//        $buttons[] = $this->generateViewButton(
//            Url::generate('/mollie/payment-link/view', [
//                'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
//            ]),
//            'View'
//        );
//
//        $buttons[] = $this->generateEditButton(
//            Url::generate('/mollie/payment-link/edit', [
//                'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
//            ]),
//            'Edit'
//        );
//
//
//        $buttons[] = $this->generateRemoveButton(
//            Url::generate('/mollie/payment-link/delete', [
//                'id-payment-link' => $paymentLink[SpyMolliePaymentLinkTableMap::COL_ID_MOLLIE_PAYMENT_LINK],
//            ]),
//            'Delete'
//        );

        return implode(' ', $buttons);
    }
}

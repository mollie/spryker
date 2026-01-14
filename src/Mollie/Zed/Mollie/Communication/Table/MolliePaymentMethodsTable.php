<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Table;

use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class MolliePaymentMethodsTable extends AbstractTable
{
    /**
     * @var string
     */
    protected const KEY_COLUMN = 'column';

    /**
     * @var string
     */
    protected const KEY_DIRECTION = 'dir';

    /**
     * @var string
     */
    protected const SORT_DESCENDING = 'desc';

    /**
     * @var array
     */
    protected const MOLLIE_PAYMENT_METHODS_TABLE_COLUMN_MAP = [
        MolliePaymentMethodTransfer::ID => 'Name',
        MolliePaymentMethodTransfer::STATUS => 'Status',
        MolliePaymentMethodTransfer::MINIMUM_AMOUNT => 'Minimal amount',
        MolliePaymentMethodTransfer::MAXIMUM_AMOUNT => 'Maximal amount',
        MolliePaymentMethodTransfer::ISSUERS => 'Issuer List',
        MolliePaymentMethodTransfer::IMAGE => 'Images',
    ];

    /**
     * @var array
     */
    protected const MOLLIE_PAYMENT_METHODS_TABLE_RAW_COLUMNS = [
        MolliePaymentMethodTransfer::IMAGE,
    ];

    /**
     * @param \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider $dataProvider
     */
    public function __construct(
        private MolliePaymentMethodsDataProvider $dataProvider,
    ) {
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader(static::MOLLIE_PAYMENT_METHODS_TABLE_COLUMN_MAP);
        $this->mapRawColumns($config);

        $url = Url::generate('table')->build();
        $config->setDefaultSortField(MolliePaymentMethodTransfer::ID);
        $config->setUrl($url);
        $config->setSortable(
            [
                MolliePaymentMethodTransfer::STATUS,
                MolliePaymentMethodTransfer::ID,
                MolliePaymentMethodTransfer::MINIMUM_AMOUNT,
                MolliePaymentMethodTransfer::MAXIMUM_AMOUNT,
            ],
        );

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $responseTransfer = $this->dataProvider->getData();
        $paymentMethodsCollection = $responseTransfer->getCollection();

        return $this->processData($paymentMethodsCollection->getMethods()->getArrayCopy());
    }

    protected function processData(array $paymentMethods): array
    {
        $results = [];
        $searchTerm = $this->getSearchTerm()['value'];

        foreach ($paymentMethods as $paymentMethod) {
            if ($searchTerm) {
                if (!$this->isSearchTermFound($paymentMethod->getId(), $searchTerm)) {
                    continue;
                }
            }

            $results[] = [
                MolliePaymentMethodTransfer::ID => $paymentMethod->getId(),
                MolliePaymentMethodTransfer::STATUS => $paymentMethod->getStatus(),
                MolliePaymentMethodTransfer::MINIMUM_AMOUNT => $this->formatMinimumAmountField($paymentMethod),
                MolliePaymentMethodTransfer::MAXIMUM_AMOUNT => $this->formatMaximumAmountField($paymentMethod),
                MolliePaymentMethodTransfer::ISSUERS => $this->formatIssuerList($paymentMethod->getIssuers()),
                MolliePaymentMethodTransfer::IMAGE => $this->formatImagesField($paymentMethod->getImage()),
            ];
        }

        $sortingParameters = $this->createSortingParameters($this->getOrderParameter())[0];
        if ($sortingParameters) {
            $results = $this->sortResults($results, $sortingParameters[static::KEY_COLUMN], $sortingParameters[static::KEY_DIRECTION]);
        }

        $this->filtered = count($results);
        $this->total = count($paymentMethods);

        return $this->paginateResults($results);
    }

    /**
     * @param array $results
     *
     * @return array
     */
    protected function paginateResults(array $results): array
    {
        return array_slice($results, $this->getOffset(), $this->getLimit());
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function mapRawColumns(TableConfiguration $config): void
    {
        $rawColumns = static::MOLLIE_PAYMENT_METHODS_TABLE_RAW_COLUMNS;

        foreach ($rawColumns as $column) {
            $config->addRawColumn($column);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodTransfer $transfer
     *
     * @return string
     */
    protected function formatMinimumAmountField(MolliePaymentMethodTransfer $transfer): string
    {
        $min = $transfer->getMinimumAmount();
        if (!$min) {
            return '0.00';
        }

        return $min['value'] . ' ' . $min['currency'];
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodTransfer $transfer
     *
     * @return string
     */
    protected function formatMaximumAmountField(MolliePaymentMethodTransfer $transfer): string
    {
        $max = $transfer->getMaximumAmount();
        if (!$max) {
            return 'unlimited';
        }

        return $max['value'] . ' ' . $max['currency'];
    }

    /**
     * @param array $issuers
     *
     * @return string
     */
    protected function formatIssuerList(array $issuers): string
    {
        $html = '';
        foreach ($issuers as $key => $issuer) {
            $html .= "{$issuer}, ";
        }

        return $html;
    }

    /**
     * @param array $images
     *
     * @return string
     */
    protected function formatImagesField(array $images): string
    {
        $html = '';
        foreach ($images as $key => $imageUrl) {
            $html .= "<img src=\"{$imageUrl}\">";
        }

        return $html;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    protected function isSearchTermFound(string $haystack, string $needle): bool
    {
        if (str_contains(strtolower($haystack), strtolower($needle))) {
            return true;
        }

        return false;
    }

    /**
     * @param array $results
     * @param string $columnIndex
     * @param string $sortDirection
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function sortResults(array $results, string $columnIndex, string $sortDirection): array
    {
        $columns = static::MOLLIE_PAYMENT_METHODS_TABLE_COLUMN_MAP;

        if (is_numeric($columnIndex)) {
            $columnIndex = (int)$columnIndex;
        }

        if (!isset(array_keys($columns)[$columnIndex])) {
            throw new InvalidArgumentException("Invalid column index: $columnIndex");
        }

        usort($results, function ($a, $b) use ($sortDirection, $columns, $columnIndex) {
            if (!array_key_exists(array_keys($columns)[$columnIndex], $a) || !array_key_exists(array_keys($columns)[$columnIndex], $b)) {
                return 0;
            }

            if ($sortDirection === static::SORT_DESCENDING) {
                /** @var array<array<int|string>> $b*/

                /** @var array<array<int|string>> $a*/
                return $b[array_keys($columns)[$columnIndex]] <=> $a[array_keys($columns)[$columnIndex]];
            }
            /** @var array<array<int|string>> $b*/

            /** @var array<array<int|string>> $a*/
            return $a[array_keys($columns)[$columnIndex]] <=> $b[array_keys($columns)[$columnIndex]];
        });

        return $results;
    }
}

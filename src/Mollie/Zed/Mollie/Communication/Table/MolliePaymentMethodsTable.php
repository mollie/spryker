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
    protected const string KEY_COLUMN = 'column';

    protected const string KEY_DIRECTION = 'dir';

    protected const string SORT_DESCENDING = 'desc';

    protected const string AMOUNT_VALUE = 'value';

    protected const string MIN_VALUE_DEFAULT = '0';

    protected const string MAX_VALUE_DEFAULT = 'unlimited';

    protected const string AMOUNT_CURRENCY = 'currency';

    protected const string IMAGE_HTML = '<img src="%s">';

    protected const string STATUS_NOT_ACTIVATED = 'not activated';

    protected const array MOLLIE_PAYMENT_METHODS_TABLE_COLUMN_MAP = [
        MolliePaymentMethodTransfer::DESCRIPTION => 'Name',
        MolliePaymentMethodTransfer::STATUS => 'Status',
        MolliePaymentMethodTransfer::MINIMUM_AMOUNT => 'Minimal amount',
        MolliePaymentMethodTransfer::MAXIMUM_AMOUNT => 'Maximal amount',
        MolliePaymentMethodTransfer::IMAGE => 'Images',
    ];

    protected const array MOLLIE_PAYMENT_METHODS_TABLE_RAW_COLUMNS = [
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
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config->setHeader(static::MOLLIE_PAYMENT_METHODS_TABLE_COLUMN_MAP);
        $this->mapRawColumns($config);

        $queryParams = $this->generateQueryParams();
        $url = Url::generate('table', $queryParams)->build();
        $config->setUrl($url);

        return $config;
    }

    /**
     * @return array<string, string>
     */
    protected function generateQueryParams(): array
    {
        $queryParams = [];

        $urlParams = $this->request->query->all();

        foreach ($urlParams as $key => $value) {
            $queryParams[$key] = $value;
        }

        return $queryParams;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array<int, mixed>
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $responseTransfer = $this->dataProvider->getData($this->request);
        $paymentMethodsCollection = $responseTransfer->getCollection();

        return $this->processData($paymentMethodsCollection->getMethods()->getArrayCopy());
    }

    /**
     * @param array<string, mixed> $paymentMethods
     *
     * @return array<int, mixed>
     */
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
                MolliePaymentMethodTransfer::DESCRIPTION => $paymentMethod->getDescription(),
                MolliePaymentMethodTransfer::STATUS => $paymentMethod->getStatus() ?? static::STATUS_NOT_ACTIVATED,
                MolliePaymentMethodTransfer::MINIMUM_AMOUNT => $this->formatMinimumAmountField($paymentMethod),
                MolliePaymentMethodTransfer::MAXIMUM_AMOUNT => $this->formatMaximumAmountField($paymentMethod),
                MolliePaymentMethodTransfer::IMAGE => $this->formatImagesField($paymentMethod->getImage()),
            ];
        }

        $this->filtered = count($results);
        $this->total = count($paymentMethods);

        return $this->paginateResults($results);
    }

    /**
     * @param array<int, mixed> $results
     *
     * @return array<int, mixed>
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
            return static::MIN_VALUE_DEFAULT;
        }

        return $min[static::AMOUNT_VALUE] . ' ' . $min[static::AMOUNT_CURRENCY];
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
            return static::MAX_VALUE_DEFAULT;
        }

        return $max[static::AMOUNT_VALUE] . ' ' . $max[static::AMOUNT_CURRENCY];
    }

    /**
     * @param array<string, mixed> $images
     *
     * @return string
     */
    protected function formatImagesField(array $images): string
    {
        $html = '';
        foreach (array_values($images) as $imageUrl) {
            $html .= sprintf(static::IMAGE_HTML, $imageUrl);
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
}

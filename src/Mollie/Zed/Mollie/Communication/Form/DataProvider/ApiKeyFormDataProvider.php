<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Mollie\Zed\Mollie\Communication\Form\ApiKeyForm;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToStoreFacadeInterface;

class ApiKeyFormDataProvider
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        protected MollieToStoreFacadeInterface $storeFacade,
    ) {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            ApiKeyForm::OPTION_STORE_CHOICES => $this->getStoreChoices(),
        ];
    }

    /**
     * @return mixed
     */
    protected function getStoreChoices()
    {
        return $this->storeFacade->getAllStores();
    }
}

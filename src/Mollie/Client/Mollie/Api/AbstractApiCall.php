<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\MollieApiClient;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

abstract class AbstractApiCall implements ApiCallInterface
{
    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     */
    public function __construct(protected MollieApiClient $mollieApiClient)
    {
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    abstract protected function getApiResponse(array $query): MollieApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    abstract protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): AbstractTransfer
    {
        $query = $this->buildQuery($mollieApiRequestTransfer);
        $mollieResponseApiTransfer = $this->getApiResponse($query);

        return $this->formatApiResponse($mollieResponseApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return array<string, mixed>
     */
    protected function buildQuery(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): array
    {
        if (!$mollieApiRequestTransfer) {
            return [];
        }

        return $mollieApiRequestTransfer->toArray();
    }
}

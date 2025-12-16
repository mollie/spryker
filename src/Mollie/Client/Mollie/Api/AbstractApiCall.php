<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Http\Request;
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
     * @param \Mollie\Api\Http\Request $request
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    abstract protected function send(Request $request): MollieApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    abstract protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer;

    /**
     * @ MOLSPRY-22 --> Contains implementation of fetching api key for mollie
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): AbstractTransfer
    {
        $request = $this->buildRequest($mollieApiRequestTransfer);
        $this->mollieApiClient->setApiKey('test_4g2NwDFgMmHG8yTnueaMmse2Vkta9t');
        $mollieResponseApiTransfer = $this->send($request);

        return $this->formatApiResponse($mollieResponseApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    abstract protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request;
}

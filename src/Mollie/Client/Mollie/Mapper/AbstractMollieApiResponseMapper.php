<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Mollie\Api\Resources\BaseResource;
use stdClass;

abstract class AbstractMollieApiResponseMapper implements MollieApiResponseMapperInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function mapDataToArray(mixed $data): mixed
    {
        if ($data === null) {
            return null;
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->mapDataToArray($value);
            }

            return $result;
        }

        if ($data instanceof stdClass || $data instanceof BaseResource) {
            return $this->mapDataToArray(get_object_vars($data));
        }

        return $data;
    }
}

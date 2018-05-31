<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Zed\PriceProductDataImport\Business\Model\Step;

use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\PriceProductDataImport\Business\Exception\InvalidPriceDataKeyException;
use Spryker\Zed\PriceProductDataImport\Business\Model\DataSet\PriceProductDataSet;

class PreparePriceDataStep implements DataImportStepInterface
{
    /**
     * @var array
     */
    protected $priceDataKeysCache = [];

    /**
     * @var bool
     */
    protected $isDataKeysCachePrepared = false;

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $this->preparePriceDataKeysCache($dataSet);
        $dataSet[PriceProductDataSet::KEY_PRICE_DATA] = $this->getPriceData($dataSet);
        $dataSet[PriceProductDataSet::KEY_PRICE_DATA_CHECKSUM] = $this->getPriceDataChecksum($dataSet[PriceProductDataSet::KEY_PRICE_DATA]);
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    protected function preparePriceDataKeysCache(DataSetInterface $dataSet): void
    {
        if ($this->isDataKeysCachePrepared) {
            return;
        }
        foreach ($dataSet as $key => $value) {
            if (!$this->isPriceDataKey($key)) {
                continue;
            }
            $this->priceDataKeysCache[$key] = $this->getPriceDataKey($key);
        }
        $this->isDataKeysCachePrepared = true;
    }

    /**
     * @param string $key
     *
     * @throws \Spryker\Zed\PriceProductDataImport\Business\Exception\InvalidPriceDataKeyException
     *
     * @return string
     */
    protected function getPriceDataKey(string $key): string
    {
        $keyParts = explode('.', $key);
        if (count($keyParts) < 2) {
            throw new InvalidPriceDataKeyException(
                sprintf(
                    'Price data key "%s" has invalid format. Should be in following format: "price_data.some_key"',
                    $key
                )
            );
        }

        return $keyParts[1];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function isPriceDataKey(string $key): bool
    {
        return mb_strpos($key, PriceProductDataSet::KEY_PRICE_DATA_PREFIX) === 0;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return string
     */
    protected function getPriceData(DataSetInterface $dataSet): string
    {
        $priceData = [];

        foreach ($this->priceDataKeysCache as $dataSetKey => $priceDataKey) {
            $priceData = $this->addPriceDataValue($priceData, $priceDataKey, $dataSet[$dataSetKey]);
        }

        return json_encode($priceData);
    }

    /**
     * @param array $priceData
     * @param string $key
     * @param string $value
     *
     * @return array
     */
    protected function addPriceDataValue(array $priceData, string $key, string $value): array
    {
        if (empty($value)) {
            $priceData[$key] = [];

            return $priceData;
        }

        $priceData[$key] = json_decode($value, true);

        return $priceData;
    }

    /**
     * @param string $priceData
     *
     * @return string
     */
    protected function getPriceDataChecksum(string $priceData): string
    {
        return hash('crc32b', $priceData);
    }
}
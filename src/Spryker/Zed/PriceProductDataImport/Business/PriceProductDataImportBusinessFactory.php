<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductDataImport\Business;

use Spryker\Zed\DataImport\Business\DataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\PriceProductDataImport\Business\Model\PriceProductWriterStep;
use Spryker\Zed\PriceProductDataImport\Business\Model\Step\AbstractSkuToIdProductAbstractStep;
use Spryker\Zed\PriceProductDataImport\Business\Model\Step\ConcreteSkuToIdProductStep;
use Spryker\Zed\PriceProductDataImport\Business\Model\Step\CurrencyToIdCurrencyStep;
use Spryker\Zed\PriceProductDataImport\Business\Model\Step\PreparePriceDataStep;
use Spryker\Zed\PriceProductDataImport\Business\Model\Step\StoreToIdStoreStep;

/**
 * @method \Spryker\Zed\PriceProductDataImport\PriceProductDataImportConfig getConfig()
 */
class PriceProductDataImportBusinessFactory extends DataImportBusinessFactory
{
    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImporterAfterImportAwareInterface|\Spryker\Zed\DataImport\Business\Model\DataImporterBeforeImportAwareInterface|\Spryker\Zed\DataImport\Business\Model\DataImporterInterface|\Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerAwareInterface
     */
    public function createPriceProductDataImport()
    {
        $dataImporter = $this->getCsvDataImporterFromConfig($this->getConfig()->getPriceProductDataImporterConfiguration());

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep($this->createAbstractSkuToIdProductAbstractStep());
        $dataSetStepBroker->addStep($this->createConcreteSkuToIdProductStep());
        $dataSetStepBroker->addStep($this->createStoreToIdStoreStep());
        $dataSetStepBroker->addStep($this->createCurrencyToIdCurrencyStep());
        $dataSetStepBroker->addStep($this->createPreparePriceDataStep());
        $dataSetStepBroker->addStep(new PriceProductWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createAbstractSkuToIdProductAbstractStep(): DataImportStepInterface
    {
        return new AbstractSkuToIdProductAbstractStep();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createConcreteSkuToIdProductStep(): DataImportStepInterface
    {
        return new ConcreteSkuToIdProductStep();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createStoreToIdStoreStep(): DataImportStepInterface
    {
        return new StoreToIdStoreStep();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createCurrencyToIdCurrencyStep(): DataImportStepInterface
    {
        return new CurrencyToIdCurrencyStep();
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface
     */
    protected function createPreparePriceDataStep(): DataImportStepInterface
    {
        return new PreparePriceDataStep();
    }
}
<?php declare(strict_types=1);

namespace CrimsonAgility\ProductsRange\Controller\Products;

use Exception;
use CrimsonAgility\ProductsRange\Block\Product\ProductsList;
use Magento\CatalogWidget\Model\Rule\Condition\Combine;
use Magento\CatalogWidget\Model\Rule\Condition\Product;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout;
use Magento\Widget\Helper\Conditions as ConditionsHelper;

class RangeData extends AbstractAccount implements HttpGetActionInterface
{
    private const PRODUCT_GRID_TEMPLATE = 'CrimsonAgility_ProductsRange::product/widget/content/grid.phtml';
    private const ALLOWED_SORT_ORDER = ['price_low_to_high', 'price_high_to_low'];

    /**
     * @var Layout
     */
    protected Layout $layout;

    /**
     * @var ConditionsHelper
     */
    protected ConditionsHelper $conditionsHelper;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @param Context $context
     * @param Layout $layout
     * @param ConditionsHelper $conditionsHelper
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        Layout $layout,
        ConditionsHelper $conditionsHelper
    ) {
        $this->layout = $layout;
        $this->conditionsHelper = $conditionsHelper;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @return ?Raw
     * @throws Exception
     */
    public function execute(): ?Raw
    {
        $request = $this->context->getRequest();
        $priceFrom = (float) $request->getParam('price-from');
        $priceTo = (float) $request->getParam('price-to');
        $sortOrder = $request->getParam('sort-order');

        if (!in_array($sortOrder, self::ALLOWED_SORT_ORDER) ||
            $priceFrom >= $priceTo || $priceTo > ($priceFrom * 5)) {
            throw new Exception((string) __('Invalid data.'));
        }

        $output = $this->layout->getLayout()
            ->createBlock(ProductsList::class)
            ->setData('sort_order', $sortOrder)
            ->setData('conditions_encoded', $this->getConditionsEncoded($priceFrom, $priceTo))
            ->setTemplate(self::PRODUCT_GRID_TEMPLATE)
            ->toHtml();

        /** @var Raw $rawResult */
        $rawResult = $this->context->getResultFactory()->create(ResultFactory::TYPE_RAW);
        return $rawResult->setContents($output);
    }

    /**
     * @param float $from
     * @param float $to
     * @return string
     */
    private function getConditionsEncoded(float $from, float $to): string
    {
        $condition['1']['type'] = Combine::class;
        $condition['1']['aggregator'] = 'all';
        $condition['1']['value'] = '1';
        $condition['1']['new_child'] = '';
        $condition['1--1']['type'] = Product::class;
        $condition['1--1']['attribute'] = 'price';
        $condition['1--1']['operator'] = '&gt;=';
        $condition['1--1']['value'] = (string) $from;
        $condition['1--2']['type'] = Product::class;
        $condition['1--2']['attribute'] = 'price';
        $condition['1--2']['operator'] = '&lt;=';
        $condition['1--2']['value'] = (string) $to;

        return $this->conditionsHelper->encode($condition);
    }
}

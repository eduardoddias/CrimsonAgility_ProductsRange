<?php declare(strict_types=1);

namespace CrimsonAgility\ProductsRange\Test\Unit\Block\Product;

use CrimsonAgility\ProductsRange\Block\Product\ProductsList;
use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\Http\Context as ContextAlias;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Helper\Conditions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ProductsListTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected MockObject $getSalableQuantityDataBySku;

    /**
     * @var ProductsList
     */
    protected ProductsList $productsList;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productCollectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $catalogProductVisibility = $this->getMockBuilder(Visibility::class)
            ->disableOriginalConstructor()
            ->getMock();
        $httpContext = $this->getMockBuilder(ContextAlias::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sqlBuilder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rule = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->getMock();
        $conditionsHelper = $this->getMockBuilder(Conditions::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getSalableQuantityDataBySku = $this->getMockBuilder(GetSalableQuantityDataBySku::class)
            ->disableOriginalConstructor()
            ->getMock();
        $json = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layoutFactory = $this->getMockBuilder(LayoutFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlEncoder = $this->getMockBuilder(EncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryRepository = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productsList = new ProductsList(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $this->getSalableQuantityDataBySku,
            [],
            $json,
            $layoutFactory,
            $urlEncoder,
            $categoryRepository
        );
    }

    public function testGetProductStockBySku()
    {
        $productSku = 'test-sku';
        $qty = 10.0;

        $this->getSalableQuantityDataBySku->expects($this->once())
            ->method('execute')
            ->with($productSku)
            ->willReturn([['qty' => $qty]]);

        $this->assertEquals($qty, $this->productsList->getProductStockBySku($productSku));
    }

    public function testGetProductStockBySkuThrowException()
    {
        $productSku = 'test-sku';

        $this->getSalableQuantityDataBySku->expects($this->once())
            ->method('execute')
            ->with($productSku)
            ->willThrowException(new Exception('exception message;'));

        $this->assertNull($this->productsList->getProductStockBySku($productSku));
    }
}

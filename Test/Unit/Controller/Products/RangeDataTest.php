<?php declare(strict_types=1);

namespace CrimsonAgility\ProductsRange\Test\Unit\Controller\Products;

use CrimsonAgility\ProductsRange\Block\Product\ProductsList;
use CrimsonAgility\ProductsRange\Controller\Products\RangeData;
use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Result\Layout;
use Magento\Widget\Helper\Conditions as ConditionsHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RangeDataTest extends TestCase
{
    /**
     * @var MockObject
     */
    private MockObject $contextMock;

    /**
     * @var MockObject
     */
    private MockObject $layoutMock;

    /**
     * @var MockObject
     */
    private MockObject $conditionsHelperMock;

    /**
     * @var MockObject
     */
    private MockObject $requestMock;

    /**
     * @var MockObject
     */
    private MockObject $productsListMock;

    /**
     * @var MockObject
     */
    private MockObject $rawResultMock;

    /**
     * @var RangeData
     */
    private RangeData $rangeData;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutMock = $this->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->conditionsHelperMock = $this->getMockBuilder(ConditionsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->getMock();

        $this->productsListMock = $this->getMockBuilder(ProductsList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rawResultMock = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rangeData = new RangeData(
            $this->contextMock,
            $this->layoutMock,
            $this->conditionsHelperMock
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testExecuteWithInvalidData(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid data.');

        $this->requestMock->method('getParam')
            ->willReturnMap([
                ['price-from', null, '10'],
                ['price-to', null, '15'],
                ['sort-order', null, 'invalid_sort_order']
            ]);

        $this->contextMock->method('getRequest')
            ->willReturn($this->requestMock);

        $this->rangeData->execute();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testExecuteWithValidData(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap([
                ['price-from', null, '10'],
                ['price-to', null, '15'],
                ['sort-order', null, 'price_low_to_high']
            ]);

        $this->contextMock->method('getRequest')
            ->willReturn($this->requestMock);

        $layoutInterfaceMock = $this->getMockBuilder(\Magento\Framework\View\LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $blockInstance = $this->getMockBuilder(AbstractBlock::class)
            ->onlyMethods(['toHtml'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $layoutInterfaceMock->expects($this->once())
            ->method('createBlock')
            ->with(ProductsList::class)
            ->willReturn($blockInstance);

        $this->productsListMock->expects($this->any())
            ->method('setData')
            ->willReturnMap([
                ['sort_order', 'price_low_to_high', $layoutInterfaceMock],
                ['conditions_encoded', 'some_encoded_condition', $layoutInterfaceMock]
            ]);

        $this->productsListMock->expects($this->any())
            ->method('setTemplate')
            ->willReturn($blockInstance);

        $this->layoutMock->method('getLayout')->willReturn($layoutInterfaceMock);

        $this->conditionsHelperMock->method('encode')->willReturn('encoded_data');

        $blockInstance->expects($this->once())->method('toHtml')->willReturn('html-data');

        $resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->rawResultMock->expects($this->once())->method('setContents')->willReturnSelf();

        $resultFactoryMock->expects($this->once())->method('create')->willReturn($this->rawResultMock);

        $this->contextMock->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($resultFactoryMock);

        $this->rangeData->execute();
    }
}

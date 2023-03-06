<?php declare(strict_types=1);

namespace CrimsonAgility\ProductsRange\Test\Unit\Controller\Products;

use CrimsonAgility\ProductsRange\Controller\Products\Range;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RangeTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected MockObject $context;

    /**
     * @var MockObject
     */
    protected MockObject $resultPageFactory;

    /**
     * @var MockObject
     */
    protected MockObject $messageManager;

    /**
     * @var Range
     */
    protected Range $range;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultPageFactory = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->range = new Range($this->context, $this->resultPageFactory);
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $this->context->expects($this->once())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);

        $this->messageManager->expects($this->once())
            ->method('addNoticeMessage')
            ->with(__('The "To Price" could not be greater than 5x the "From Price".'));

        $this->resultPageFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->getMockBuilder(Page::class)
                ->disableOriginalConstructor()
                ->getMock());

        $result = $this->range->execute();

        $this->assertInstanceOf(Page::class, $result);
    }
}

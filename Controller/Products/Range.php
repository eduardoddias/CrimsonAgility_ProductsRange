<?php declare(strict_types=1);

namespace CrimsonAgility\ProductsRange\Controller\Products;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Range extends AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->context = $context;

        parent::__construct($this->context);
    }

    /**
     * Forgot customer account information page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $this->context->getMessageManager()->addNoticeMessage(
            __('The "To Price" could not be greater than 5x the "From Price".')
        );
        return $this->resultPageFactory->create();
    }
}

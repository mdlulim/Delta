<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Controller\Adminhtml\Customer;

class Save extends \Consnet\ManageCustomer\Controller\Adminhtml\Customer
{
    /**
     * Upload model
     * 
     * @var \Delda\ManageCustomer\Model\Upload
     */
    protected $_uploadModel;

    /**
     * File model
     * 
     * @var \Consnet\ManageCustomer\Model\Customer\File
     */
    protected $_fileModel;

    /**
     * Image model
     * 
     * @var \Consnet\ManageCustomer\Model\Customer\Image
     */
    protected $_imageModel;

    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * constructor
     * 
     * @param \Consnet\ManageCustomer\Model\Upload $uploadModel
     * @param \Consnet\ManageCustomer\Model\Customer\File $fileModel
     * @param \Consnet\ManageCustomer\Model\Customer\Image $imageModel
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Consnet\ManageCustomer\Model\CustomerFactory $CustomerFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Consnet\ManageCustomer\Model\Upload $uploadModel,
        \Consnet\ManageCustomer\Model\Customer\File $fileModel,
        \Consnet\ManageCustomer\Model\Customer\Image $imageModel,
        \Consnet\ManageCustomer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_uploadModel    = $uploadModel;
        $this->_fileModel      = $fileModel;
        $this->_imageModel     = $imageModel;
        $this->_backendSession = $backendSession;
        parent::__construct($customerFactory, $registry, $resultRedirectFactory);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getCustomer('customer');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->_filterData($data);
            $customer = $this->_initCustomer();
            $customer->setData($data);
            $featuredImage = $this->_uploadModel->uploadFileAndGetName('featured_image', $this->_imageModel->getBaseDir(), $data);
            $customer->setFeaturedImage($featuredImage);
            $sampleUploadFile = $this->_uploadModel->uploadFileAndGetName('sample_upload_file', $this->_fileModel->getBaseDir(), $data);
            $customer->setSampleUploadFile($sampleUploadFile);
            $this->_eventManager->dispatch(
                'consnet_canagecustomer_customer_prepare_save',
                [
                    'customer' => $customer,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $customer->save();
                $this->messageManager->addSuccess(__('The Customer has been saved.'));
                $this->_backendSession->setConsnetManageCustomerData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'consnet_managecustomer/*/edit',
                        [
                            'entity_id' => $customer->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('consnet_managecustomer/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Customer.'));
            }
            $this->_getSession()->setConsnetManageCustomerData($data);
            $resultRedirect->setPath(
                'consnet_managecustomer/*/edit',
                [
                    'entity_id' => $customer->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('consnet_managecustomer/*/');
        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     * @return array
     */
    protected function _filterData($data)
    {
        if (isset($data['sample_multiselect'])) {
            if (is_array($data['sample_multiselect'])) {
                $data['sample_multiselect'] = implode(',', $data['sample_multiselect']);
            }
        }
        return $data;
    }
}

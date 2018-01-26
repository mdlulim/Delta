<?php
/**
 * Consnet_ManageCustomer extension
 *                     NOTICE OF LICENSE
 */
namespace Consnet\ManageCustomer\Controller\Adminhtml\Customer;

class Delete extends \Consnet\ManageCustomer\Controller\Adminhtml\Customer
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            $name = "";
            try {
                /** @var \Consnet\ManageCustomer\Model\Customer $customer */
                $customer = $this->_customerFactory->create();
                $customer->load($id);
                $name = $customer->getName();
                $customer->delete();
                $this->messageManager->addSuccess(__('The Customer has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_consnet_managecustomer_customer_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('consnet_managecustomer/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_consnet_managecustomer_customer_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('consnet_managecustomer/*/edit', ['entity_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Customer to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('consnet_managecustomer/*/');
        return $resultRedirect;
    }
}

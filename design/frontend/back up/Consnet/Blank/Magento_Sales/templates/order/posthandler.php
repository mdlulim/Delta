<?php
class RegisterUser {
    private $orderitems;

    function __construct() {/*var_dump($_POST);*/
        
        $this->orderitems = isset($_POST['sku1']) ? $_POST['sku1'] : null;
        
        /*var_dump("<br/>");
        var_dump($this->orderitems);*/
    }

    function start() {
        if (empty($this->orderitems)) {
            return "Empty Post not allowed";
        } 
        else
        {
            // Do some stuiff
            return "Makale";//$this->orderitems;//" Registration Done";
        }
    }
}

$register = new RegisterUser();
if(!empty($_POST))
{

    $result = $register->start();
    header( 'Location: https://www.google.com' ) ;
    echo $result;
}

<?php
namespace Consnet\Erporder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $orderitems;

    protected $resultPageFactory;
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderitems = array(array('',''));
        for($i = 1; $i < (count($_POST) / 2) + 1; $i++){
            //$temp = array('' => , );
            //$orderitems = array($_POST['sku'.$i], $_POST['qty'.$i]);
            $orderitems[$i] = array($_POST['sku'.$i], $_POST['qty'.$i]);
            //array_push($this->orderitems, array($_POST['sku'.$i], $_POST['qty'.$i]));
            //$this->orderitems = $_POST['sku'.$i], $_POST['qty'.$i]
        }
        //array_push($this->orderitems, array($_POST['sku'.$i], $_POST['qty'.$i]));
        /*$this->orderitems = array();
        var_dump($_POST);
        if (isset($_POST['sku2'])) {
            echo "Empty Post not allowed";
        } 
        else
        {
            // Do some stuiff
            echo "Makale";//$this->orderitems;//" Registration Done";
        }*/
        //$this->orderitems = isset($_POST['sku1']) ? $_POST['sku1'] : null;
        //echo 'Count: '.(count($_POST) / 2);
        print_r($orderitems);
        echo $_POST['sku'.'1'].$_POST['qty'.'1'];
        echo "Makale Madihlaba<br/>";
    }
}

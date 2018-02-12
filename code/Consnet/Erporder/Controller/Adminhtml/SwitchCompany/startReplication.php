<?php

namespace Consnet\ErpOrder\Controller\Adminhtml\SwitchCompany;

use  \Magento\Backend\App\Action\Context;
use \Magento\User\Model\UserFactory;
use \Magento\User\Model\ResourceModel\User;
use SoapFault;

class startReplication extends \Magento\Framework\App\Action\Action {

    const COMPANY_EMAIL = 'stores@delta.co.zw';
    const CUSTOMER_EMAIL = 'users@delta.co.zw';
    const COMPANYADMIN_EMAIL = 'admin@delta.co.zw';
    const COMPANYUSER_EMAIL = 'companyuser@delta.co.zw';


    protected $NAMESPACE_ID;
    protected $_resultPageFactory;
    protected $_logger;
    protected $storeManager;
    protected $customerFactory;
    protected $address;
    protected $customerRepository;
    protected $companyRepository;
    protected $companyFactory;
    protected $objectManager;
    protected $cust;
    protected $cont;
    protected $bf;
    protected $addr;
    protected $companyManagement;
    protected $companyCustomerInterface;
    protected $token;
    protected $userFactory;
    protected $products;
    //constants
    protected $userResourceModel;
    private $_connection;
    private $_resource;
    protected $next_batch_min;
    protected $username;
    protected $password;
    protected $size;
    protected $init_repl;
    protected $halt;
    protected $fileLocation;
    protected $soapClient2;

    protected $admin_user ;
    protected $admin_pass;

    /**
     * @var \Magento\Company\Model\UserRoleFactory
     */
    private $userRoleFactory;

      /**
     * @var \Magento\Company\Model\ResourceModel\Role\CollectionFactory
     */
    private $roleCollectionFactory;

     /**
     * @var \Magento\Company\Model\RoleFactory
     */
    private $roleFactory;

    //protected $appState ;

    /**
     * @param \Magento\Company\Api\Data\CompanyInterfaceFactory $companyFactory
     * 
     * 
     */

    public function __construct( \Magento\Backend\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory  )
     { 
      
       

        

        // $filePath = BP. '/var/log/replicatinlog.txt';
        // echo $filePath;
        // $log  = fopen($filePath ,'a') or die('unable to open file');
        // fwrite($log,'Constructor run '.date('h:i:sa') ."\n");
        // fclose($log);
        parent::__construct($context); 
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resultPageFactory = $resultPageFactory;
        $this->storeManager = $this->objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $this->customerFactory = $this->objectManager->create('\Magento\Customer\Model\CustomerFactory');
        $this->_logger = $this->objectManager->create('\Psr\Log\LoggerInterface');
        $this->address  = $this->objectManager->create('\Magento\Customer\Model\AddressFactory');
        $this->customerRepository = $this->objectManager->create('\Magento\Customer\Model\ResourceModel\CustomerRepository');
        $this->companyRepository = $this->objectManager->create('\Magento\Company\Model\CompanyRepository');
        $this->companyFactory = $this->objectManager->create('\Magento\Company\Model\CompanyFactory');
        $this->companyCustomerInterface = $this->objectManager->create('\Magento\Company\Api\Data\CompanyInterfaceFactory');
        $this->_resources = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();
        $this->userFactory = $this->objectManager->create('\Magento\User\Model\UserFactory');
        $this->userResourceModel =$this->objectManager->create('\Magento\User\Model\ResourceModel\User');
        $this->userRoleFactory =  $this->objectManager->create('\Magento\Company\Model\ResourceModel\Role\CollectionFactory');
        $this->roleCollectionFactory = $this->objectManager->create('\Magento\Company\Model\ResourceModel\Role\CollectionFactory');
        $this->roleFactory = $this->objectManager->create('\Magento\Company\Model\RoleFactory'); 
        $this->objectManager->create('\Magento\User\Model\ResourceModel\User');



        $wsdlUrl = dirname(__FILE__) . "/z_bp_rep_v6.xml";



        $helper = $this->objectManager->create('Consnet\Api\Helper\Data');
        $WURL = $helper->getGeneralConfig('replication_text');
        $this->init_repl = $helper->getGeneralConfig('initial_text');
        $this->username = $helper->getGeneralConfig('user_name');
        $this->password = $helper->getGeneralConfig('password');
        $this->size = $helper->getGeneralConfig('size_text');
        $this->halt = $helper->getGeneralConfig('halt_text');
        $this->next_batch_min = $helper->getGeneralConfig('last_row_text');
        $this->NAMESPACE_ID = $helper->getGeneralConfig('namespace_text');
        $this->fileLocation = $helper->getGeneralConfig('customer_file_location_text');
        $this->admin_user  =  $helper->getGeneralConfig('admin_user');
        $this->admin_pass  =  $helper->getGeneralConfig('admin_password');


        if ($this->init_repl == 'X') {

        } else {
            $this->init_repl = null;
        }

        

        $dataExist = $this->getTable('ERP_CUSTOMER');

        if(count( $dataExist) > 0 ){
           if($dataExist[0]['KUNNR'] == null ){
            $this->deleteTable('ERP_CUSTOMER');
            $this->deleteTable('ERP_CONTACT');
            $this->deleteTable('ERP_ORG');
            $this->deleteTable('ERP_ADDRESS');


            $dataExist = $this->getTable('ERP_CUSTOMER');
           }
        }
       

       if(count($dataExist) <1 ){


        $parameters = array(
            "IV_MIN" => $this->next_batch_min,
            "IV_MAX" => ( $this->size + $this->next_batch_min ),
            "IV_DELTA_REP" => $this->init_repl, // or 'X'
            "IV_FROM_DATE" => null,
            "IV_TO_DATE" => null
        );


      //  $WURL = $helper->getGeneralConfig('replication_text');
        $this->soapClient2 = new \Zend\Soap\Client($WURL, array("soap_version" => SOAP_1_2));

        //Set Login details
        $this->soapClient2->setHttpLogin($this->username);
        $this->soapClient2->setHttpPassword($this->password);



        try{
            $result = $this->soapClient2->ZZ_BP_REPLICATION($parameters);
            
            }catch(SoapFault $e){
                
        $filePath = BP. '/var/log/replicatinlog.txt';
        echo $filePath;
        $log  = fopen($filePath ,'a') or die('unable to open file');
        fwrite($log,'in catch : '.date('h:i:sa') ."\n");
        fclose($log);
            }
            
        $filePath = BP. '/var/log/replicatinlog.txt';
        echo $filePath;
        $log  = fopen($filePath ,'a') or die('unable to open file');
        fwrite($log,'after catch : '.date('h:i:sa') ."\n");
        fclose($log);

        if (isset($result)) {

            
        $filePath = BP. '/var/log/replicatinlog.txt';
        echo $filePath;
        $log  = fopen($filePath ,'a') or die('unable to open file');
        fwrite($log,'resut set : '.date('h:i:sa') ."\n");
        fclose($log);

            $this->cont = $result->EX_CONTACT_PERSONS;
            $this->cust = $result->EX_GENERAL_CUST_DATA;
            $this->bf = $result->EX_ORG_CORP;
            $this->addr = $result->EX_ADDRESS;
            $this->products = $result->EX_PRODUCTS;
            $this->next_batch_min = $result->EX_LAST_INDEX;
            
            
           
            $this->createTable('ERP_CUSTOMER', $this->cust);
            $this->createTable('ERP_CONTACT', $this->cont);
            $this->createTable('ERP_ORG', $this->bf);
            
            $this->createTable('ERP_ADDRESS', $this->addr);
           // $this->createTable('ERP_PRODUCTS', $this->products);



        } else {

            $filePath = BP. '/var/log/replicatinlog.txt';
            echo $filePath;
            $log  = fopen($filePath ,'a') or die('unable to open file');
            fwrite($log,'resut not set : '.date('h:i:sa') ."\n");
            fclose($log);

            $this->cont = null;
            $this->cust = null;
            $this->bf = null;
            $this->addr = null;
            $this->products = null;
            $this->next_batch_min = 0;

     
        }


        $this->authApi();

    }else{




        $this->cont = null;
        $this->cust = null;
        $this->bf = null;
        $this->addr = null;
        $this->products = null;
        //$this->next_batch_min = 0;


        $this->authApi();

       // parent::__construct($context);

    }
    
}
    

    protected function authApi() {
        $userData = array("username" => $this->admin_user , "password" => $this->admin_pass );
        $ch = curl_init( $this->NAMESPACE_ID . "/index.php/rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $this->token = curl_exec($ch);
        
    }


    private function is_cli(){
        return !http_response_code();
    }



    public function execute() {

        $filePath = BP. '/var/log/replicatinlog.txt';
        $log  = fopen($filePath ,'a') or die('unable to open file');
        fwrite($log,'execute run '.date('h:i:sa') ."\n");
        fclose($log);



        $stop = 1;
        $loop_counter = 1;
        $helper = $this->objectManager->create('Consnet\Api\Helper\Data');
      
       
        $this->loadProducts();
        $this->createAdminUsers();
        $this->createCompany();
        $this->createCompanyUsers();


        $this->deleteTable('ERP_CUSTOMER');
        $this->deleteTable('ERP_CONTACT');
        $this->deleteTable('ERP_ORG');
        $this->deleteTable('ERP_ADDRESS');
        //$this->deleteTable('ERP_PRODUCTS');

        $helper->setConfigValue('last_row_text', $this->next_batch_min);
        $newMax = $this->size + $this->next_batch_min;

        if ($this->halt == 1) {

            $resultPage = $this->_resultPageFactory->create();
            return $resultPage;
        } else {
            $loopIndex =  0;

            while ($stop !== 0) {

                $loopIndex +=  1;

                $parameters = array(
                    "IV_MIN" => $this->next_batch_min,
                    "IV_MAX" => $newMax,
                    "IV_DELTA_REP" => $this->init_repl, // or 'X'
                    "IV_FROM_DATE" => null,
                    "IV_TO_DATE" => null
                );
               // $this->soapClient2->ZZ_BP_REPLICATION($parameters);
                try{
                    $WURL = $helper->getGeneralConfig('replication_text');
                    $this->soapClient2 = new \Zend\Soap\Client($WURL, array("soap_version" => SOAP_1_2));
            
                    //Set Login details
                    $this->soapClient2->setHttpLogin($this->username);
                    $this->soapClient2->setHttpPassword($this->password);




                $result = $this->soapClient2->ZZ_BP_REPLICATION($parameters);
                }catch(SoapFault $e){
                    $helper->setConfigValue('customer_file_location_text', $e->getMessage());
                }
                if (isset($result)) {
                    $this->cont = $result->EX_CONTACT_PERSONS;
                    $this->cust = $result->EX_GENERAL_CUST_DATA;
                    $this->bf = $result->EX_ORG_CORP;
                    $this->addr = $result->EX_ADDRESS;
                    $this->products = $result->EX_PRODUCTS;
                    $this->next_batch_min = $result->EX_LAST_INDEX;

                    $this->createTable('ERP_CUSTOMER', $this->cust);
                    $this->createTable('ERP_CONTACT', $this->cont);
                    $this->createTable('ERP_ORG', $this->bf);
                    $this->createTable('ERP_ADDRESS', $this->addr);
                   // $this->createTable('ERP_PRODUCTS', $this->products);

                } else {
                    $this->cont = null;
                    $this->cust = null;
                    $this->bf = null;
                    $this->addr = null;
                    $this->products = null;
                    $this->next_batch_min = 0;
                }

                $helper->setConfigValue('last_row_text', $this->next_batch_min);


                $this->createAdminUsers();


                if ($this->cust !== null) {
                    $this->createCompany();
                    $this->createCompanyUsers();
                } else {
                    break;
                }

                $this->deleteTable('ERP_CUSTOMER');
                $this->deleteTable('ERP_CONTACT');
                $this->deleteTable('ERP_ORG');
                $this->deleteTable('ERP_ADDRESS');
               // $this->deleteTable('ERP_PRODUCTS');



                $newMax = $this->size + $this->next_batch_min;
                $helper->setConfigValue('last_row_text', $this->next_batch_min);

                if ($loop_counter == $this->halt) {
                    $stop = 0;
                }
                if ($this->next_batch_min == 0) {
                    $stop = 0;
                }
                $loop_counter ++;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        
        return $resultPage;
    }

    //returns mysql fetchAll results
    public function createTable($name,$data){

        if(!isset($data)){
            exit;
        }
        if($name  ==  'ERP_ADDRESS' && property_exists($data ,'item')){
            
            if(is_array($data->item)){
                foreach($data->item as $line ){
                    foreach($line as $key=>$col){
                     if($key == 'ADRC_UUID') {
                        $line->ADRC_UUID = null ;
                    }
                 }
                }
            }else{

            foreach($data->item as $key=>$line ){
   
                if($key == 'ADRC_UUID') {
                    $data->item->ADRC_UUID = null ;
                }
            }
        }
    }


        $sql = "SHOW TABLES LIKE '".$name."'";
        $found = $this->_connection->fetchAll($sql);
       
        $count =  0 ;
        foreach($found as $table){
            $count ++;
        }

        if($count > 0 ){
            //table exist
            //JUST FILL DATA
            $array  = json_decode(json_encode($data),true);
            $this->InsertData($name,$array);
        }else{
            //create table
            $array  = json_decode(json_encode($data),true);
            $sql = "CREATE TABLE ".$name." ( ";
            $sql = $sql." `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY ,";

            if(is_array($array) && isset($array['item'][0])){
            foreach($array['item'][0]  as $key=>$attributes){

                $sql .= "`$key` VARCHAR(50) ,";
             }
            }
             $sql = rtrim($sql,',');
             $sql .= ");";
             $this->_connection->query($sql);
             $array  = json_decode(json_encode($data),true);
             $this->InsertData($name,$array);
        }
        $sql  = 'SELECT * FROM '.$name;
        return $this->_connection->fetchAll($sql);
    }

    public function InsertData($table,$data){

        //var_dump($data);die();
       
        $sql =  'SELECT * FROM '.$table;
        $rows = $this->_connection->fetchAll($sql);

        $count  =  count($rows);
        if($count > 0 ){
            //we cannot insert , must read only
        }else{

        if(isset($array[0][1])){
            $count = 2 ;
        }else{
            $count = 1 ;
        }

        $array = $data ;

       
      
    
      if(isset($array['item'][1]) && is_array($array) ){

           
        $i = count($array['item']);
        $row = array();

         for($x=0;  $x<=($i -1 ); $x++){

            $line = $array['item'];
            
             foreach($line[$x] as $key=> $r){
              $row[$key]=$r;
            }
            $this->_connection->insert($table,$row);
            $row = array();
         }
        }elseif($count == 1){
            $row = array();

            $line = $array['item'];
            if(is_array($line)){
                $columns = array_keys($line);

                foreach($columns as $col ){
               
                    $row[$col]=$line[$col];
                 }
            }
          
          
           $this->_connection->insert($table,$row);
           $row = array();
 
        }
    }
 
    }

    public function deleteTable($name){

        $sql = "SHOW TABLES LIKE '".$name."'";
        $found = $this->_connection->fetchAll($sql);
        $count =  0 ;
        foreach($found as $table){
            $count ++;
        }

        if($count < 1 ){

        }else{
        $sql  = 'DELETE FROM '.$name;
        $this->_connection->query($sql);
        }
    }

    public function getTable($name){

        $sql = "SHOW TABLES LIKE '".$name."'";
        $found = $this->_connection->fetchAll($sql);
        $count =  0 ;
        foreach($found as $table){
            $count ++;
        }

        if($count > 0){

        $sql  = 'SELECT * FROM '.$name;
        return $this->_connection->fetchAll($sql);
        }else{
            return [] ;
        }

    }

    public function joinTables($table1,$table2,$key1,$val1,$key2,$val2){

        $sql  = 'SELECT * FROM '.$table1.' WHERE '.$key1.' = "'.$val1.'"';
        $re1  =  $this->_connection->fetchAll($sql);

    }

    public function joinTable($table,$key,$val , $and = '' ){

      $sql  = 'SELECT * FROM '.$table.' WHERE `'.$key.'` = "'.$val.'" '. $and;

      $res =  $this->_connection->fetchAll($sql);
      if(count($res) > 0 ){
          return $res[0];
      }
      return  false;
   }

    public function cleanStr($v) {

        $v = str_replace("'", "", $v);
        $v = str_replace("/", "", $v);
        $v = str_replace("\\", "", $v);
        $v = str_replace(" ", "", $v);
        $v = str_replace("(", "", $v);
        $v = str_replace(")", "", $v);
        $v = str_replace("=", "", $v);
        $v = str_replace("_", "", $v);
        $v = str_replace("&", "", $v);
        $v = str_replace("#", "", $v);
        $v = str_replace("!", "", $v);
        $v = str_replace("{", "", $v);
        $v = str_replace("}", "", $v);
        $v = str_replace("[", "", $v);
        $v = str_replace("]", "", $v);
        $v = str_replace(",", "", $v);
        $v = str_replace("?", "", $v);
        $v = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $v);
              return $v;
    }

    public function createAdminUsers() {

        $users = $this->getTable('ERP_CONTACT');
      
        $roleId =   0;

        $sql = "SELECT * FROM `authorization_role` WHERE `role_name` = 'sales_rep'  ";
        $roles = $this->_connection->fetchAll($sql);

        foreach($roles as $role ){
            $roleId = $role['role_id'];

        }
        if($roleId == 0 ){

            var_dump('Please maintain admin roles for users');
            die();
         
         }

        if(!$users){
        foreach ($users as $user) {

            if (isset($user['ABTNR']) && $user['ABTNR'] == 'PF') {

                if(empty($user['NAME1'])){
                    $user['NAME1'] = $user['NAMEV'];
                }
                if(empty($user->NAMEV)){

                    $user['NAMEV'] = $user['NAME1'];
                }

                $username = $this->cleanStr($user['NAME1']);
                $email = $user['NAME1'] . $user['NAMEV'] . 'admin@delta.co.zw';
                $email = $this->cleanStr($email);
                $admin = $this->userFactory->create()->loadByUsername($username);
                if ($admin->getId()) {
                    //update
                    $admin->setUsername($this->cleanStr($username));
                    $admin->setFirstname($this->cleanStr($user['NAME1']));
                    $admin->setLastname($this->cleanStr($user['NAMEV']));
                    $admin->setIsActive(1);
                    $admin->setRoleId($roleId);
                    $admin->setEmail($email);
                    try{
                    $admin->save();
                    }catch(\Exception $e)
                    {
                        continue;
                    }
                } else {
                    //create
                    //var_dump($username);
                    $admin = $this->userFactory->create();
                    $admin->setUsername($this->cleanStr($username));
                    $admin->setFirstname($this->cleanStr($user['NAME1']));
                    $admin->setLastname($this->cleanStr($user['NAMEV']));
                    $admin->setEmail($email);
                    $admin->setPassword('Consnet01');
                    $admin->setIsActive(1);
                    $admin->setRoleId($roleId);
                    try{
                        $admin->save();
                        }catch(\Exception $e)
                        {
                            continue;
                           // $admin = $this->userFactory->create()->loadByUsername($username);
                        }
                }
            }
        }
     }
    }

    public function createCompany() {

        $CUSTOMER = $this->getTable('ERP_CUSTOMER');

        

        $loopcount = 0;
        foreach ($CUSTOMER as $erp_customer) {

            $loopcount++;

            //get ompany address
            $address = $this->joinTable('ERP_ADDRESS','ADDRNUMBER',$erp_customer['ADRNR']) ;
            $_cust_email = '';

            //check if email is used by contact person
            if(isset($address['NAME4']) ){
                 $user = $this->joinTable('ERP_ADDRESS','NAME4',$address['NAME4'] ,  'AND CLIENT = ""' ) ;

                    if(isset($user['NAME4']))
                    {  
                        //var_dump($user['NAME4']);
                        $address['NAME4']  = $erp_customer['KUNNR'].SELF::COMPANY_EMAIL ;
                    }
                     $_cust_email = $address['NAME4'];
           }else{

            $_cust_email =  $erp_customer['KUNNR'].SELF::COMPANY_EMAIL ;
            $_cust_email = $this->cleanStr($_cust_email);
           }

           if (!filter_var($_cust_email, FILTER_VALIDATE_EMAIL)) {
              $_cust_email  =  $erp_customer['KUNNR'].SELF::COMPANY_EMAIL ;
           }

           

            //get business data
            $extededAttr = $this->joinTable('ERP_ORG','KUNNR',$erp_customer['KUNNR']);
            
            if (!$extededAttr) {
                $cgroup = $this->getCustomerGroup($extededAttr['VKORG']);
            }else {
                $cgroup = 1;
            }

    
            $companyRepo = $this->objectManager->get('\Magento\Company\Model\CompanyRepository');
            $comp_id = $this->_connection->fetchRow("SELECT * FROM company  WHERE company_email = '" . $_cust_email . "' ");
            
            $company = $this->companyFactory->create();
            $company->load($comp_id['entity_id']);

            

            //get ompany address
           
            $salesuser = $this->getSalesRep($erp_customer['KONZS']) ;



            if (!$company->getId()){

                $dummyUser = $this->createDummyAdminUser($erp_customer['NAME1'],$erp_customer['KUNNR']);

                if (empty($address['TEL_NUMBER'])) {
                    $address['TEL_NUMBER'] = '000 000 000';
                }
                if (empty($address['MC_NAME1'])) {
                    $address['MC_NAME1'] = 'Address Name not Maintained';
                }
                if (empty($address['STREET'])) {
                    $address['STREET'] = 'Street Address not maintained';
                }
                if (empty($address['CITY1'])) {
                    $address['CITY1'] = 'ZW';
                }
                if (empty($address['REGION'])) {
                    $address['REGION'] = 'ZW';
                }
                if (empty($address['COUNTRY'])) {
                    $address['COUNTRY'] = 'ZW';
                }



                $company = $this->companyFactory->create();
                $company->setCompanyName($erp_customer['NAME1']);
                $company->setLegalName($address['MC_NAME1']);
                $company->setCompanyEmail($_cust_email);
                $company->setStatus(1);
                $company->setCustomerGroupId($cgroup);
                $company->setStreet($address['STREET']);
                $company->setCity($address['CITY1']);
                $company->setCountryId($address['COUNTRY']);
                $company->setRegion($address['REGION']);
                $company->setPostcode("99999");
                $company->setTelephone($address['TEL_NUMBER']);
                $company->setSuperUserId($dummyUser);
                $company->setSalesRepresentativeId($salesuser);

                

                $company->setData('ZTERM', $erp_customer['KTOKD']);
                $company->setData('VKORG', $extededAttr['VKORG']);
                $company->setData('PARVW', $extededAttr['PARVW']);
                $company->setData('SPART', $extededAttr['SPART']);
                $company->setData('VTWEG', $extededAttr['VTWEG']);
                $company->setData('STP_ID',$extededAttr['KUNNR']);
                $company->setData('PLANT', $erp_customer['SORTL']);
                //GET AND SET  CUSTOMER GROUP
                //$company->setCustomerGroupId($this->getCustomerGroup($extededAttr['VKORG']));
                $company->save();
               
                $this->setCustomerAdmin($company->getId(),$dummyUser);

                $sql = "SELECT * FROM `company_credit` WHERE 	company_id = ".$company->getId();
                $credit = $this->_connection->fetchAll($sql);
                if( is_array($credit)){
                    //credit exisit
                 }else{
                    $sql = "INSERT INTO `company_credit`( `company_id`, `credit_limit`, `balance`, `currency_code`, `exceed_limit`) VALUES (".$company->getId().",1,1,'USD',1)  ";
                    $credit = $this->_connection->query($sql);

                 }

                $_role = $this->createCompanyRole($company->getId());

                $cust_kunnr = $erp_customer['KUNNR'];
                //$this->createCompanyUsers($company->getId(), $cust_kunnr);
            } else {

                //get ompany address

                if (empty($address['TEL_NUMBER'])) {
                    $address['TEL_NUMBER'] = '000 000 000';
                }
                if (empty($address['MC_NAME1'])) {
                    $address['MC_NAME1'] = 'Address Name not Maintained';
                }
                if (empty($address['STREET'])) {
                    $address['STREET'] = 'Street Address not maintained';
                }
                if (empty($address['CITY1'])) {
                    $address['CITY1'] = 'ZW';
                }
                if (empty($address['REGION'])) {
                    $address['REGION'] = 'ZW';
                }
                if (empty($address['COUNTRY'])) {
                    $address['COUNTRY'] = 'ZW';
                }


                $company->setCompanyName($erp_customer['NAME1']);
                $company->setLegalName($address['MC_NAME1']);
                $company->setCompanyEmail($_cust_email);
                $company->setStatus(1);
                $company->setCustomerGroupId($cgroup);
                $company->setStreet($address['STREET']);
                $company->setCity($address['CITY1']);
                $company->setCountryId($address['COUNTRY']);
                $company->setRegion($address['REGION']);
                $company->setPostcode("99999");
                $company->setTelephone($address['TEL_NUMBER']);
                $company->setSalesRepresentativeId($salesuser);
               
                $company->setData('ZTERM', $erp_customer['KTOKD']);
                $company->setData('VKORG', $extededAttr['VKORG']);
                $company->setData('PARVW', $extededAttr['PARVW']);
                $company->setData('SPART', $extededAttr['SPART']);
                $company->setData('VTWEG', $extededAttr['VTWEG']);
                $company->setData('STP_ID',$extededAttr['KUNNR']);
                $company->setData('PLANT', $erp_customer['SORTL']);
                //GET AND SET  CUSTOMER GROUP
               // $company->setCustomerGroupId($this->getCustomerGroup($extededAttr['VKORG']));
                 $company->save();
            
                $this->setCustomerAdmin($company->getId(),$company->getSuperUserId());
                try{

                $CreditData  = $this->objectManager->create('Magento\CompanyCredit\Model\CreditDataProvider')->get($company->getId());   
               
                }catch(\Magento\Framework\Exception\NoSuchEntityException $ex){
                    $CreditData = null ;
                }
                $_role = $this->createCompanyRole($company->getId());
                $cust_kunnr = $erp_customer['KUNNR'];

                $sql = "SELECT * FROM `company_credit` WHERE 	company_id = ".$company->getId();
                $credit = $this->_connection->fetchAll($sql);
                if( is_array($credit)){
                    //credit exisit
                 }else{
                    $sql = "INSERT INTO `company_credit`( `company_id`, `credit_limit`, `balance`, `currency_code`, `exceed_limit`) VALUES (".$company->getId().",1,1,'USD',1)  ";
                    $credit = $this->_connection->query($sql);

                 }

                
              
                
            }

            //var_dump( $company->getId());
        }
    }

    protected function getBusinesData($obj, $id) {

        $array = $obj;

        foreach ($array->item as $element) {
            if ($id == $element->KUNNR) {
                return $element;
            }
        }

        return false;
    }

    protected function getSalesRep($PERNR) {

        $lv_pernr = ltrim($PERNR, '0');
        $user = $this->joinTable('ERP_CONTACT','PARNR',$lv_pernr);
        $uid = 0;
        if(!$user){
            $user = $user[0];
            $email = $user['NAME1'] . $user['NAMEV'] . '@delta.co.zw';
            $admin = $this->userFactory->create()->loadByUsername( $this->cleanStr( $user['NAME1'] ) );
            $uid = $admin->getId();
        }
        if ($uid == 0) {
            $uid = 1;
        }
        return $uid;

    }

    protected function getBusinesAddress($obj, $id) {

        $array = $obj;

        foreach ($array->item as $element) {
            if ($id == $element->ADDRNUMBER) {
                return $element;
            }
        }

        return false;
    }

    public function createDummyAdminUser($name,$kunnr) {



        $email = $kunnr. self::COMPANYUSER_EMAIL;


        $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
        $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($email);

        if(!$customer->getId()){
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($email);
        $customer->setFirstname($name);
        $customer->setLastname('Company '.$kunnr);

        $customer->setData('CONTACT_ID', $kunnr);
        $customer->save();
        }

        return $customer->getId();
    }

    public function crearObject($url, $operation, $data, $log) {

       
        $_result = null;
        try{
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $operation);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($this->token)));

        $_result = curl_exec($ch);
        $_result = json_decode($_result, 1);


        if ($log == 'X') {
            // var_dump($data);
            // var_dump($_result);
        }
    }catch(Exception $ex){
        var_dump($ex->getMessage());
    }
        return $_result;
    }

    public function createCompanyRole($company_id) {



        $sql = "INSERT INTO `company_roles`(`sort_order`, `role_name`, `company_id`) VALUES ( 0 ,'Default User',".$company_id.")  ";
        $this->_connection->query($sql);

        $role = [
            "role" => [
                "role_name" => "Super User Person",
                "permissions" => [
                    ["resource_id" => "Magento_Company::index", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::all", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::place_order", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::payment_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders_sub", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_address", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::payment_information", "permission" => "allow"],
                ],
                "company_id" => $company_id
            ]
        ];
        $url =  $this->NAMESPACE_ID . "/index.php/rest/V1/company/role";

        $role = $this->crearObject($url, 'POST', $role, '');

        return $role;
    }



    public function updateCompanyRole($company_id, $roleid) {

        $role = [
            "role" => [
                "role_name" => "Super User Person",
                "permissions" => [
                    ["resource_id" => "Magento_Company::index", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::all", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::place_order", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::payment_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders_sub", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_address", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::payment_information", "permission" => "allow"],
                ],
                "company_id" => $company_id
            ]
        ];
        $url =  $this->NAMESPACE_ID . "/index.php/rest/V1/company/role/" . $roleid;

        $role = $this->crearObject($url, 'PUT', $role, '');

        return $role;
    }

    public function getRolesByCompanyId($companyId)
    {
        $roleCollection = $this->roleCollectionFactory->create();
        $roleCollection->addFieldToFilter('company_id', ['eq' => $companyId])
            ->addFieldToFilter('role_name', ['eq' => 'Super User Person'])
            ->setOrder('role_id', 'ASC')
            ->load();
        $roles = $roleCollection->getItems();

        foreach($roles as $role){
            return $role;
            break;
        }

        return $roles;
    }

    public function createCompanyUsers() {

       

        $CONTACTS = $this->getTable('ERP_CONTACT');
        $loop_c = 0;
        $current_user_id = 0;


        foreach ($CONTACTS as $contact) {

            if (!isset($contact['KUNNR'])) {

                continue;
            }
            $sql = "SELECT * FROM `company` WHERE STP_ID ='" . $contact['KUNNR'] . "'";
            $company = $this->_connection->fetchRow($sql);




            if (isset($company['STP_ID'])) {


                if(empty($contact['NAME1'])){
                    $contact['NAME1'] = $contact['NAMEV'];
                }
                if(empty($contact['NAMEV'])){

                    $contact['NAMEV'] = $contact['NAME1'];
                }

                $persn = $contact['UEPAR'];
                
                $address = $this->joinTable('ERP_ADDRESS','ADDRNUMBER',$persn) ;
                $_cust_email = $address['NAME4'];
               
 
                $filePath = BP. '/var/log/replicatinlog.txt';
                
                $log  = fopen($filePath ,'a') or die('unable to open file');
                fwrite($log,$_cust_email.': '.date('h:i:sa') ."\n");
                fclose($log);


                if (!filter_var($_cust_email, FILTER_VALIDATE_EMAIL)) {
                   continue;
                }




                $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId)->loadByEmail($_cust_email);

                $loop_c = $loop_c + 1;
                $o_customer_Id = $customer->getId();


                if ($customer->getId()) {

                    //update
                    $id = $customer->getId();

                    $customer = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($id);
                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail($_cust_email);
                    $customer->setGroupId($company['customer_group_id']);
                    $customer->setFirstname($contact['NAME1']);
                    $customer->setLastname($contact['NAMEV']);
                    $customer->setStoreId(1);
                    $customer->setData('CONTACT_ID', $contact['PARNR']);
                    $customerExt = $customer->getExtensionAttributes();
                    $repo = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');

                    if ($customerExt == null) {

                        $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
                        $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');
                        $customerCompanyExt->setCustomerId($id);
                        $customerCompanyExt->setCompanyId($company['entity_id']);
                        $customerCompanyExt->setJobTitle("Contact Person");
                        $customerCompanyExt->setStatus(1);
                        $customerCompanyExt->setTelephone("000-000-0000");
                        $customerExt->setCompanyAttributes($customerCompanyExt);
                        $customer->setExtensionAttributes($customerExt);

                    } else {



                        $customer->getExtensionAttributes()->setContactId($contact['PARNR']);
                        if ($customer->getExtensionAttributes()->getCompanyAttributes() !== null) {
                            $customer->getExtensionAttributes()->getCompanyAttributes()->setCustomerId($id);
                            $customer->getExtensionAttributes()->getCompanyAttributes()->setCompanyId($company['entity_id']);
                            $customer->getExtensionAttributes()->getCompanyAttributes()->setJobTitle("Contact Person");
                            $customer->getExtensionAttributes()->getCompanyAttributes()->setStatus(1);
                            $customer->getExtensionAttributes()->getCompanyAttributes()->setTelephone("000-000-0000");
                        }
                    }





                    $customer = $repo->save($customer);

                    $role  = $this->getRolesByCompanyId($company['entity_id']);
                    $UserRole = $this->objectManager->create('Magento\Company\Model\Action\Customer\Assign');
                    $UserRole->assignCustomerRole($customer, $role->getId()); //assignRoles


                    //var_dump($customer->getExtensionAttributes()->getCompanyAttributes()->getJobTitle());
                    //check if user is in  multi company table  , here  user to company 1:n , but company to user  1:1

                    $sql = "SELECT * FROM `consnet_multi_customer_user` WHERE  `user_id` = " . $customer->getId() . " AND `company_id` = " . $company['entity_id'];
                    $multy_user = $this->_connection->fetchAll($sql);
                    if (!(count($multy_user) >= 1)) {
                        //add user into the multi  map
                        //add user into the multi  map

                        $sql = "INSERT INTO `consnet_multi_customer_user`  (`user_id`, `company_id`, `name`) VALUES (" . $customer->getId() . "," . $company['entity_id'] . ",'" . $this->cleanStr($company['company_name']) . "')";
                        $this->_connection->query($sql);
                    }
                } else {
                    //create



                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail($_cust_email);
                    $customer->setGroupId($company['customer_group_id']);
                    $customer->setFirstname($contact['NAME1']);
                    $customer->setLastname($contact['NAMEV']);
                    $customer->setData('CONTACT_ID', $contact['PARNR']);
                    $customer->setStoreId(1);

                    try{
                    $id = $customer->save()->getId();
                    }catch(\Magento\Framework\Exception\AlreadyExistsException $ex){
                        var_dump($_cont_email);
                        die();
                    }

                    $customerExt = $customer->getExtensionAttributes();

                    $customer = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer->getId());
                    $customerExt = $customer->getExtensionAttributes();
                    $repo = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');



                    if ($customerExt == null) {

                        $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
                        $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');

                        $customerCompanyExt->setCustomerId($id);
                        $customerCompanyExt->setCompanyId($company['entity_id']);
                        $customerCompanyExt->setJobTitle("Contact Person");
                        $customerCompanyExt->setStatus(1);
                        $customerCompanyExt->setTelephone("000-000-0000");
                        $customerExt->setCompanyAttributes($customerCompanyExt);
                        $customer->setExtensionAttributes($customerExt);
                    } else {

                        $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
                        $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');

                        $customerCompanyExt->setCustomerId($id);
                        $customerCompanyExt->setCompanyId($company['entity_id']);
                        $customerCompanyExt->setJobTitle("Contact Person");
                        $customerCompanyExt->setStatus(1);
                        $customerCompanyExt->setTelephone("000-000-0000");
                        $customerExt->setCompanyAttributes($customerCompanyExt);
                        $customer->setExtensionAttributes($customerExt);
                    }


                    $customer = $repo->save($customer);

                    $role  = $this->getRolesByCompanyId($company['entity_id']);
                    $UserRole = $this->objectManager->create('Magento\Company\Model\Action\Customer\Assign');
                    $UserRole->assignCustomerRole($customer, $role->getId()); //assignRoles


                    $sql = "INSERT INTO `consnet_company_user`  (`user_email`, `company_id`) VALUES ('" . $customer->getEmail() . "'," . $company['entity_id'] . ")";
                    $this->_connection->query($sql);


                    //add user into the multi  map

                    $sql = "INSERT INTO `consnet_multi_customer_user`  (`user_id`, `company_id`, `name`) VALUES (" . $customer->getId() . "," . $company['entity_id'] . ",'" . $this->cleanStr($company['company_name']) . "')";
                    $this->_connection->query($sql);
                }
            }
            //break;
        }
    }

    public function setCustomerAdmin($company,$customer)
    {
        $customer = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer);

       
        $customerExt = $customer->getExtensionAttributes();
        //var_dump($customerExt->getCompanyAttributes()->getJobTitle());die();
       // var_dump($customerExt);die();
        $repo = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');

    try{

        if ($customerExt == null) {

            var_dump('in');die();

            $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
            $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');

            $customerCompanyExt->setCustomerId($customer);
            $customerCompanyExt->setCompanyId($company);
            $customerCompanyExt->setJobTitle("Company Admin");
            $customerCompanyExt->setStatus(1);
            $customerCompanyExt->setTelephone("000-000-0000");
            $customerExt->setCompanyAttributes($customerCompanyExt);
            $customer->setExtensionAttributes($customerExt);
            $customer = $repo->save($customer);

        } else {

            // $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
            // $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');

            // $customerCompanyExt->setCustomerId($customer);
            // $customerCompanyExt->setCompanyId($company);
            // $customerCompanyExt->setJobTitle("Company Admin");
            // $customerCompanyExt->setStatus(1);
            // $customerCompanyExt->setTelephone("000-000-0000");
            // $customerExt->setCompanyAttributes($customerCompanyExt);
            // $customer->setExtensionAttributes($customerExt);
            // $customer = $repo->save($customer);
        }
    }catch(\Magento\Framework\Exception\CouldNotSaveException $ex){
        var_dump($ex->getMessage()); 
    }



    }

    protected function getCustomerGroup($vkorg) {
        $id = 0;
        switch ($vkorg) {
            case 'YW01':
                $id = 4;
                break;
            case 'YW02':
                $id = 5;
                break;
            case 'YW03':
                $id = 6;
                break;
            case 'YW05':
                $id = 1;
                break;
        };

        return $id;
    }

    public function createUser() {

    }


    public function loadProducts() {
        $eccproducts = $this->products;
        if(isset($eccproducts)){
        try {
            $filePath = BP . '/var/import/products/products.csv';
            $csv_handler = fopen($filePath, 'w');
            //var_dump($filePath); die();


            fputcsv($csv_handler, $this->getHeaderArray());

            $productList = array(array());
            $i = 0;

           
            foreach ($eccproducts->item as $item) {
                if (!in_array($item->MATNR, array_column($productList, 0))) {
                    if ($item->KONDM == 'Y1' || $item->KONDM == 'Y2' || $item->KONDM == 'Y3') {
                        $category = $this->getProductCategory($item->KONDM);

                        $productList[$i] = array(
                            /* 1 */
                            $item->MATNR, '', 'Default', 'simple', $category,
                            /* 2 */
                            'base', $item->MAKTX, '', '', '', 1,
                            /* 3 */
                            "Taxable Goods", "Catalog, Search", 1.0000, '', '',
                            /* 4 */
                            '', '', $item->MAKTX, $item->MAKTX,
                            /* 5 */
                            $item->MAKTX, $item->MATNR . '.jpg', '',
                            /* 6 */
                            $item->MATNR . '.jpg', '', $item->MATNR . '.jpg', '',
                            /* 7 */
                            $item->MATNR . '.jpg', '', "10/17/17, 12:00 PM", '', '',
                            /* 8 */
                            '', "Block after Info Column", '', '',
                            /* 9 */
                            '', '', '', '', '',
                            /* 10 */
                            '', '', '', '', 'Zimbabwe',
                            /* 11 */
                            '', 9999999.0000, 0.0000,
                            /* 12 */
                            1, 0, 0, 1, 1.0000,
                            /* 13 */
                            1, 10000.0000, 1, 1,
                            /* 14 */
                            1.0000, 1, 1,
                            /* 15 */
                            1, 1, 1.0000,
                            /* 16 */
                            1, 0, 0,
                            /* 17 */
                            0, 1, 1, '',
                            /* 18 */
                            '', '', '', '', '',
                            /* 19 */
                            $item->MATNR . '.jpg', '', '', '',
                            /* 20 */
                            '', '', '',
                            /* 21 */
                            '', '', '',
                            /* 22 */
                            '', ''
                        );
                        $i++;
                    }
                }
            }
        

            foreach ($productList as $product) {
                fputcsv($csv_handler, $product);
            }
            fclose($csv_handler);
            $this->assignProductsToSharedCatalogs();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
    }

    protected function getHeaderArray() {
        return array(
            /* 1 */
            'sku', 'store_view_code', 'attribute_set_code', 'product_type', 'categories',
            /* 2 */
            'product_websites', 'name', 'description', 'short_description', 'weight',
            /* 3 */
            'product_online', 'tax_class_name', 'visibility', 'price', 'special_price',
            /* 4 */
            'special_price_from_date', 'special_price_to_date', 'url_key', 'meta_title',
            /* 5 */
            'meta_keywords', 'meta_description', 'base_image', 'base_image_label',
            /* 6 */
            'small_image', 'small_image_label', 'thumbnail_image', 'thumbnail_image_label',
            /* 7 */
            'swatch_image', 'swatch_image_label', 'created_at', 'updated_at', 'new_from_date',
            /* 8 */
            'new_to_date', 'display_product_options_in', 'map_price', 'msrp_price',
            /* 9 */
            'map_enabled', 'gift_message_available', 'custom_design', 'custom_design_from',
            /* 10 */
            'custom_design_to', 'custom_layout_update', 'page_layout', 'product_options_container',
            /* 11 */
            'msrp_display_actual_price_type', 'country_of_manufacture', 'additional_attributes',
            /* 12 */
            'qty', 'out_of_stock_qty', 'use_config_min_qty', 'is_qty_decimal', 'allow_backorders',
            /* 13 */
            'use_config_backorders', 'min_cart_qty', 'use_config_min_sale_qty', 'max_cart_qty',
            /* 14 */
            'use_config_max_sale_qty', 'is_in_stock', 'notify_on_stock_below',
            /* 15 */
            'use_config_notify_stock_qty', 'manage_stock', 'use_config_manage_stock',
            /* 16 */
            'use_config_qty_increments', 'qty_increments', 'use_config_enable_qty_inc',
            /* 17 */
            'enable_qty_increments', 'is_decimal_divided', 'website_id', 'deferred_stock_update',
            /* 18 */
            'use_config_deferred_stock_update', 'related_skus', 'related_position', 'crosssell_skus',
            /* 19 */
            'crosssell_position', 'upsell_skus', 'upsell_position', 'additional_images',
            /* 20 */
            'additional_image_labels', 'hide_from_product_page', 'custom_options',
            /* 21 */
            'bundle_price_type', 'bundle_sku_type', 'bundle_price_view', 'bundle_weight_type',
            /* 22 */
            'bundle_values', 'bundle_shipment_type', 'configurable_variations',
            /* 23 */
            'configurable_variation_labels'//, 'associated_skus'
        );
    }

    protected function getProductCategory($group) {
        switch ($group) {
            case 'Y1': //Y1 Sparkling //2,3,4,5
                return "Default Category/Products/Sparkling Beverages";
                break;
            case 'Y2': //Y2 Lagers
                return "Default Category/Products/Lager";
                break;
            case 'Y3': //Y3 Sorghum
                return "Default Category/Products/Sorghum";
                break;
        }
    }

    protected function assignProductsToSharedCatalogs() {
        //Get Collection
        //Set to Shared Catalog based on group
    }

}

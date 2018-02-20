<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Consnet\Promotions\Controller\Index;

use Magento\Framework\View\Element\Template;
use DeltaPromo\Promo\Model\ResourceModel\Demo\PromoCollection as PromoCollection;

// require_once('app/Mage.php');

/**
 * Description of CronJobPromotion
 *
 * @author Kanyinda
 */
class CronJobPromotion {

    private $list = array();

    protected $helper ; 
    protected $WURL ;
    protected $UserName ;
    protected $Password ;

    public function getMyListOfMatErp($stp_id) {

        $this->doCleanUp ();
        

        try {
            $wsdlUrl = dirname(__FILE__) . "/myWSLD_p.xml";
            $WURL = "http://deltapq01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/srvc_url/sap/bc/srt/rfc/sap/zz_magento_get_promo_wsdl_1/300/zz_magento_service/zz_magento_binding?sap-client=300";
                     
            $parameters = array("BpNumber" => $stp_id);



            //  $soapClient2 = new \Zend\Soap\Client($wsdlUrl, array("soap_version" => SOAP_1_2));
            $opts = array('http' => array('user_agent' => 'PHPSoapClient'));
            $context = stream_context_create($opts);
            $soapClientOptions = array('stream_context' => $context, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2);
            libxml_disable_entity_loader(false);

          //  $soapClient2 = new \Zend\Soap\Client($WURL, $soapClientOptions);
            $soapClient2 = new \Zend\Soap\Client( $this->WURL , $soapClientOptions);

            ////---////
            //Set Login details
            
            $soapClient2->setHttpLogin($this->UserName)  ;                 //('tmadihlaba');
            $soapClient2->setHttpPassword ($this->Password)  ;             // ('Consnet@2018');



            $tab_result = $soapClient2->ZzMagentoGetPromotions($parameters);

            $model = \Magento\Framework\App\objectManager::getInstance()->create('Consnet\Promotions\Model\Promotion');
            $list = null;
            $i = 0;
            $productsIds = [];

              
           


            if (property_exists($tab_result->ExPromoList, 'item')) {



                 if(! is_array ($tab_result->ExPromoList->item)) {
                   $productsIds[0] =        $tab_result->ExPromoList->item->Matnr ;
                    return $productsIds;
              }







                try {

                    $id = 0;
                    foreach ($tab_result->ExPromoList->item as $item) {
                        //    var_dump ($tab_result->ExPromoList->item ) ;
                        if ((isset($item)) && (property_exists($item, 'Matnr') )) {
                            $productsIds[$id] = $item->Matnr;
                            $id++;
                        }
                    }
                } catch (\Exception $exception) {
                    echo $exception;
                }
            }
            return $productsIds;
        } catch (SoapFault $e) {

            return '';
        }
    }

    public function setInitData () {

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $om->get('Consnet\Api\Helper\Data');

          
        $this->WURL = $this->helper->getGeneralConfig('promotions_text');
        $this->UserName = $this->helper->getGeneralConfig('user_name') ;
        $this->Password = $this->helper->getGeneralConfig('password') ;
    }

    public function doCleanUp () {

        //  \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList ;
          $cacheTypeList = \Magento\Framework\App\objectManager::getInstance()->create('Magento\Framework\App\Cache\TypeListInterface') ;

        //  \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool ;
          $cacheFrontendPool = \Magento\Framework\App\objectManager::getInstance()->create('Magento\Framework\App\Cache\Frontend\Pool') ;

         $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
                foreach ($types as $type) {
                    $cacheTypeList->cleanType($type);
                }
                foreach ($cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }

                $this->setInitData () ;

    }

    public function getDataFromErp($stp_id) {

          $this->doCleanUp ();

        try {
            $wsdlUrl = dirname(__FILE__) . "/myWSLD_p.xml";
             
            $parameters = array("BpNumber" => $stp_id);
            
            $opts = array('http' => array('user_agent' => 'PHPSoapClient'));
            $context = stream_context_create($opts);
            $soapClientOptions = array('stream_context' => $context, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2);
            libxml_disable_entity_loader(false);
          //  $soapClient2 = new \Zend\Soap\Client($wsdlUrl, $soapClientOptions);

            $soapClient2 = new \Zend\Soap\Client( $this->WURL , $soapClientOptions);

            ////---////
            //Set Login details
            $soapClient2->setHttpLogin($this->UserName)  ;                 //('tmadihlaba');
            $soapClient2->setHttpPassword ($this->Password)  ;             // ('Consnet@2018');



            $tab_result = $soapClient2->ZzMagentoGetPromotions($parameters);


           
            $list = null;
            $i = 0;
            $model = \Magento\Framework\App\objectManager::getInstance()->create('Consnet\Promotions\Model\Promotion');
                            
            
            if ((property_exists($tab_result->ExPromoList, 'item') ) && (isset($tab_result->ExPromoList->item) )) {
                try {

                    
                    foreach  ($tab_result->ExPromoList->item as $item) {

 

                        if ((isset($item)) 
                           
                           ) {
                            
                          $model = \Magento\Framework\App\objectManager::getInstance()->create('Consnet\Promotions\Model\Promotion');
                             
                            if(is_array ($tab_result->ExPromoList->item)) {

                           

                           $tempKnuma = $item->Knuma;
                           $model->setKnuma_promo($tempKnuma);
                            $model->setVkorg_salesorg($item->Vkorg);
                            $model->setVtweg_division($item->Vtweg);
                            $model->setSpart_distribution($item->Matnr);
                            $model->setErnam_personCreated($item->Ernam);
                            $model->setErdat_dateCreated($item->Erdat);
                            $model->setErzet_timeCreated($item->Erzet);
                            $model->setDatab_aggreementValidFrom($item->Datab);
                            $model->setDatbi_AgreementValidTo($item->Datbi);
                            $model->setBotext_DescriptionOfAgreement($item->Abrex);
                            $model->setKnumh_ConditionRecordNumber($item->Knumh);
                            $model->setKschl_ConditionType($item->Kschl);
                            $model->setKstbw_ScaleValue($item->Kstbw);
                            $model->setKonws_scaleCurrency($item->Konws);
                            $model->setCreation_time($item->Erdat);
                            $model->setUpdate_time($item->Erzet);
                            $model->setTitle($item->Botext);
                            $model->setMatnr_product($item->Matnr);
                            $model->setKunnr($stp_id);
                            $list[] = $model;   
                            }else {
                            
                            $tempKnuma = $tab_result->ExPromoList->item->Knuma;
                            $model->setKnuma_promo($tempKnuma);
                            $model->setVkorg_salesorg($tab_result->ExPromoList->item->Vkorg);
                            $model->setVtweg_division($tab_result->ExPromoList->item->Vtweg);
                            $model->setSpart_distribution($tab_result->ExPromoList->item->Matnr);
                            $model->setErnam_personCreated($tab_result->ExPromoList->item->Ernam);
                            $model->setErdat_dateCreated($tab_result->ExPromoList->item->Erdat);
                            $model->setErzet_timeCreated($tab_result->ExPromoList->item->Erzet);
                            $model->setDatab_aggreementValidFrom($tab_result->ExPromoList->item->Datab);
                            $model->setDatbi_AgreementValidTo($tab_result->ExPromoList->item->Datbi);
                            $model->setBotext_DescriptionOfAgreement($tab_result->ExPromoList->item->Abrex);
                            $model->setKnumh_ConditionRecordNumber($tab_result->ExPromoList->item->Knumh);
                            $model->setKschl_ConditionType($tab_result->ExPromoList->item->Kschl);
                            $model->setKstbw_ScaleValue($tab_result->ExPromoList->item->Kstbw);
                            $model->setKonws_scaleCurrency($tab_result->ExPromoList->item->Konws);
                            $model->setCreation_time($tab_result->ExPromoList->item->Erdat);
                            $model->setUpdate_time($tab_result->ExPromoList->item->Erzet);
                            $model->setTitle($tab_result->ExPromoList->item->Botext);
                            $model->setMatnr_product($tab_result->ExPromoList->item->Matnr);
                            $model->setKunnr($stp_id);
                            $list[] = $model;   
                            return $model ;
                            }

                        }

                       
                    }
                } catch (\Exception $exception) {
                    echo $exception;
                }
            }
            if (!$list) {
                $list[] = $model;
            }
         //   var_dump ($list) ;
            return $list;
        } catch (SoapFault $e) {
            //            \Magento\Framework\Session\SessionManagerInterface
            //         echo $e;
            return '';
        }
    }

    public function getDataFromErpAndSave() {

      $this->doCleanUp ();

        try {
            $wsdlUrl = dirname(__FILE__) . "/myWSLD_p.xml";
            //   $wsdlUrl = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zz_magento_get_promo_wsdl_1/220/zz_magento_get_promo_wsdl_1/zz_magento_binding?sap-client=220";
            ////---////
            $parameters = array("BpNumber" => '100000000');

            //  $soapClient2 = new \Zend\Soap\Client($wsdlUrl, array("soap_version" => SOAP_1_2));
            $opts = array('http' => array('user_agent' => 'PHPSoapClient'));
            $context = stream_context_create($opts);
            $soapClientOptions = array('stream_context' => $context, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2);
            libxml_disable_entity_loader(false);
         //   $soapClient2 = new \Zend\Soap\Client($wsdlUrl, $soapClientOptions);

             $soapClient2 = new \Zend\Soap\Client( $this->WURL , $soapClientOptions);
            ////---////
            //Set Login details
          
            $soapClient2->setHttpLogin($this->UserName)  ;                 //('tmadihlaba');
            $soapClient2->setHttpPassword ($this->Password)  ;             // ('Consnet@2018');



            $tab_result = $soapClient2->ZzMagentoGetPromotions($parameters);


            if (sizeof($tab_result->ExPromoList, 0) > 0) {
                for ($i = 0; $i <= count($tab_result->ExPromoList); $i++) {


//            if($this->_objectManager) {
//                $model = $this->_objectManager->create('Consnet\Promotions\Model\Promotion');
//            }else {
                    $model = \Magento\Framework\App\objectManager::getInstance()->create('Consnet\Promotions\Model\Promotion');
                    //   ->addAttributeToFilter('kunnr',['eq'=>100000000])   ;
//            }
                    //$model->load(1);
                    // $model->setKnuma_promo($tab_result->ExPromoList->item[0]->Knuma );  
                    $model->setKnuma_promo($tab_result->ExPromoList->item[$i]->Knuma);
                    $model->setVkorg_salesorg($tab_result->ExPromoList->item[$i]->Vkorg);
                    $model->setVtweg_division($tab_result->ExPromoList->item[$i]->Vtweg);
                    $model->setSpart_distribution($tab_result->ExPromoList->item[$i]->Spart);
                    $model->setErnam_personCreated($tab_result->ExPromoList->item[$i]->Ernam);
                    $model->setErdat_dateCreated($tab_result->ExPromoList->item[$i]->Erdat);
                    $model->setErzet_timeCreated($tab_result->ExPromoList->item[$i]->Erzet);
                    $model->setDatab_aggreementValidFrom($tab_result->ExPromoList->item[$i]->Datab);
                    $model->setDatbi_AgreementValidTo($tab_result->ExPromoList->item[$i]->Datbi);
                    $model->setBotext_DescriptionOfAgreement($tab_result->ExPromoList->item[$i]->Botext);
                    $model->setKnumh_ConditionRecordNumber($tab_result->ExPromoList->item[$i]->Knumh);
                    $model->setKschl_ConditionType($tab_result->ExPromoList->item[$i]->Kschl);
                    $model->setKstbw_ScaleValue($tab_result->ExPromoList->item[$i]->Kstbw);
                    $model->setKonws_scaleCurrency($tab_result->ExPromoList->item[$i]->Konws);
                    $model->setCreation_time($tab_result->ExPromoList->item[$i]->Erdat);
                    $model->setUpdate_time($tab_result->ExPromoList->item[$i]->Erzet);
                    $model->setTitle($tab_result->ExPromoList->item[$i]->Botext);
                    $model->setMatnr($tab_result->ExPromoList->item[$i]->Matnr);


                    $model->setKunnr($tab_result->ExPromoList->item[$i]->Kunnr);



                    $model->save();
                }
            }
            return '';
        } catch (SoapFault $e) {
//            \Magento\Framework\Session\SessionManagerInterface
//         echo $e;
            return '';
        }
    }

}

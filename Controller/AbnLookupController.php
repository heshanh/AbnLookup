<?php

App::uses('AbnLookupAppController', 'AbnLookup.Controller');

class AbnLookupController extends AbnLookupAppController
{
    public $uses = false;
    private $guid;
    private $service_url;

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('abn');

        $this->guid = ''; //add your GUID here - http://abr.business.gov.au/RegisterAgreement.aspx
        $this->service_url = 'http://abr.business.gov.au/abrxmlsearch/ABRXMLSearch.asmx?WSDL';
    }


    function abn()
    {
        $this->autoRender = false;

        if(empty($this->guid))
        {
            die('Invalid GUID');
        }

        $abn = $this->remove_all_but_numbers($this->request->params['abn']);


        $client = new SoapClient($this->service_url );

        $soap_params = array(
            'searchString' => $abn,
            'includeHistoricalDetails' => 'N',
            'authenticationGuid' => $this->guid,
        );

        $result = $client->__soapCall('SearchByABNv201408', array($soap_params));

        $ent_name = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
        $ent_type = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode;
        $ent_type_desc = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityDescription;

        if(empty($result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName))
        {
            $ent_name = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
        }



        echo json_encode(array(
                'legal_entity_name' => $ent_name,
                'legal_entity_type' => $ent_type,
                'legal_entity_type_description' => $ent_type_desc,
        ));
    }


    private function remove_all_but_numbers($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }
}

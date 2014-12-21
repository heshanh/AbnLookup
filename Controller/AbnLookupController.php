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

        $entityTypeCode = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode;
        $entityDescription = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityDescription;



        switch ($result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode) {
            case 'IND':
                $entitiyName = $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->givenName;
                $entitiyName .= $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->otherGivenName  . ' ';
                $entitiyName .= $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->familyName . ' ';
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                break;
            

            case 'PTR':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            case 'PUB':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            case 'PRV':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            case 'TRT':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            default:
                # code...
                break;
        }



        echo json_encode(array(
                'entityName' => $entitiyName,
                'mainTradingName' => $mainTradingName,
                'mainName' => $mainName,
                'entityTypeCode' => $entityTypeCode,
                'entityDescription' => $entityDescription,
        ));
    }


    private function remove_all_but_numbers($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }
}

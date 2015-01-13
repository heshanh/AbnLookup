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
        $this->service_name = 'SearchByABNv201408';
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

        $result = $client->__soapCall($this->service_name, array($soap_params));

        // debug($result->ABRPayloadSearchResults);die();

        $entityTypeCode = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode;
        $entityDescription = $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityDescription;



        switch ($result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode) {
            case 'IND':
                $entitiyName_array[] = $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->givenName;
                $entitiyName_array[] = $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->otherGivenName;
                $entitiyName_array[] = $result->ABRPayloadSearchResults->response->businessEntity201408->legalName->familyName;
                $entitiyName = implode(' ', $entitiyName_array);
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                break;
            

            case 'PTR':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                // $entitiyName = $mainTradingName;
                $entitiyName = $mainName;
                break;

            case 'PUB':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            case 'PRV':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                // $entitiyName = $mainTradingName;
                $entitiyName = $mainName;
                break;

            case 'TRT':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
                break;

            case 'DTT':
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                // $entitiyName = $mainTradingName;
                $entitiyName = $mainName;
                break;

            default:
                # code...
                break;
        }

        //exceptions
        if( $result->ABRPayloadSearchResults->response->businessEntity201408->entityType->entityTypeCode == 'PUB' && empty($result->ABRPayloadSearchResults->response->businessEntity201408->mainTradingName->organisationName))
        {
                $mainTradingName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $mainName = $result->ABRPayloadSearchResults->response->businessEntity201408->mainName->organisationName;
                $entitiyName = $mainTradingName;
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

## Abn Lookup plugin for CakePHP


This plugin will help you lookup an ABN number in the australian business registry and return your business name (legal entity name) under that ABN.
It'll always return the last registred name


 * you'll need to register with the ABR to get a GUID - http://abr.business.gov.au/RegisterAgreement.aspx
 
 * Load the plugin in your bootstrap.php CakePlugin::load('AbnLookup', array('bootstrap' => false, 'routes' => true));
 * There is a predefined route for the look up /abn_lookup
 
 

 ## Installation with Composer


	{
		"require": {
			"cakephp/abnlookup": *"
		}
	}

### Enable plugin

You need to enable the plugin in your app/Config/bootstrap.php file:

`CakePlugin::load('BoostCake');`

If you are already using `CakePlugin::loadAll();`, then this is not necessary.
## How to use


http://localhost/abn_lookup/my_abn_number

* abn number will accept any valid abn with spaces, periods or dashs. 

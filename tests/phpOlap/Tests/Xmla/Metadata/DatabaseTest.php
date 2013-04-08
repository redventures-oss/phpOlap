<?php 

/*
* This file is part of phpOlap.
*
* (c) Julien Jacottet <jjacottet@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace phpOlap\Tests\Xmla\Metadata;

use phpOlap\Xmla\Metadata\Database;
use phpOlap\Xmla\Metadata\Catalog;
use phpOlap\Xmla\Connection\Connection;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $connection = $this->createConnection();
        $database = $this->createDatabase($connection);
        $catalogs1 = $database->getCatalogs();
        $catalogs2 = $database->getCatalogs();
        $catalog1 = array_shift($catalogs1);
        $catalog2 = array_shift($catalogs2);
		
		$this->assertEquals($database->getConnection(), $connection);
		$this->assertEquals($database->getName(), 'Provider=Mondrian;DataSource=MondrianFoodMart;');
		$this->assertEquals($database->getDescription(), 'Mondrian FoodMart Data Warehouse');
		$this->assertEquals($database->getUrl(), 'http://localhost:8080/mondrian/xmla');
		$this->assertEquals($database->getDataSourceInfo(), 'Provider=Mondrian;DataSource=MondrianFoodMart;');
		$this->assertEquals($database->getProviderName(), 'Mondrian');
		$this->assertEquals($database->getProviderType(), 'MDP');
		$this->assertEquals($database->getAuthenticationMode(), 'Unauthenticated');
		$this->assertEquals($catalog1->getName(), 'catalog1');
		$this->assertEquals($catalog2->getName(), 'catalog1');

	}

    public function testToArray()
    {
        $connection = $this->createConnection();
        $database = $this->createDatabase($connection);
        $arr = $database->toArray();

		$this->assertEquals($arr['name'], 'Provider=Mondrian;DataSource=MondrianFoodMart;');
		$this->assertEquals($arr['description'], 'Mondrian FoodMart Data Warehouse');
		$this->assertEquals($arr['url'], 'http://localhost:8080/mondrian/xmla');
		$this->assertEquals($arr['dataSourceInfo'], 'Provider=Mondrian;DataSource=MondrianFoodMart;');
		$this->assertEquals($arr['providerName'], 'Mondrian');
		$this->assertEquals($arr['providerType'], 'MDP');
		$this->assertEquals($arr['authenticationMode'], 'Unauthenticated');
		$this->assertEquals($arr['catalogs'][0]['name'], 'catalog1');        
    }

    private function createConnection()
    {
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findCatalogs')
					->will($this->onConsecutiveCalls(
                        array( $this->createCatalog($connection, 'catalog1') ),
                        array( $this->createCatalog($connection, 'catalog2') )
                    ));

        return $connection;
    }

    private function createDatabase($connection)
    {
		$resultSoap = '<root>
					<row>
						<DataSourceName>Provider=Mondrian;DataSource=MondrianFoodMart;</DataSourceName>
						<DataSourceDescription>Mondrian FoodMart Data Warehouse</DataSourceDescription>
						<URL>http://localhost:8080/mondrian/xmla</URL>
						<DataSourceInfo>Provider=Mondrian;DataSource=MondrianFoodMart;</DataSourceInfo>
						<ProviderName>Mondrian</ProviderName>
						<ProviderType>MDP</ProviderType>
						<AuthenticationMode>Unauthenticated</AuthenticationMode>
					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);

		$database = new Database();
		$database->hydrate($node, $connection);

        return $database;
    }

    private function createCatalog($connection, $catalogName)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>' . $catalogName . '</CATALOG_NAME>
						<DESCRIPTION>No description available</DESCRIPTION>
						<ROLES>California manager,No HR Cube</ROLES>
					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);
			
		
		$catalog = new Catalog();
		$catalog->hydrate($node, $connection);

        return $catalog;
    }

}
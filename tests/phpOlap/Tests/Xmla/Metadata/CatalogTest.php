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

use phpOlap\Xmla\Metadata\Catalog;
use phpOlap\Xmla\Metadata\Schema;

class CatalogTest extends \PHPUnit_Framework_TestCase
{
	public function testHydrate()
	{
        $connection = $this->createConnection();
		$catalog = $this->createCatalog($connection);
        $schemas1 = $catalog->getSchemas();
        $schemas2 = $catalog->getSchemas();
        $schema1 = array_shift($schemas1);
        $schema2 = array_shift($schemas2);

		$this->assertEquals($catalog->getConnection(), $connection);
		$this->assertEquals($catalog->getName(), 'FoodMart');
		$this->assertEquals($catalog->getDescription(), 'No description available');
		$this->assertEquals($catalog->getRoles(), array('California manager', 'No HR Cube'));	
		$this->assertEquals($schema1->getName(), 'schema1');
		$this->assertEquals($schema2->getName(), 'schema1');

	}

    public function testToArray()
    {
        $connection = $this->createConnection();
		$catalog = $this->createCatalog($connection);
        $arr = $catalog->toArray();

		$this->assertEquals($arr['name'], 'FoodMart');
		$this->assertEquals($arr['description'], 'No description available');
		$this->assertEquals($arr['roles'], array('California manager', 'No HR Cube'));	
		$this->assertEquals($arr['schemas'][0]['name'], 'schema1');
    }

    private function createConnection()
    {
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findSchemas')
					->will($this->onConsecutiveCalls(
                        array( $this->createSchema($connection, 'schema1') ),
                        array( $this->createSchema($connection, 'schema2') )
                    ));

        return $connection;
    }

    private function createCatalog($connection)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
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

    private function createSchema($connection, $schemaName)
    {
		$resultSoap = '<root>
					<row>
						<SCHEMA_NAME>' . $schemaName . '</SCHEMA_NAME>
					</row>
				</root>';
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);

		$node = $document->getElementsByTagName('row')->item(0);

        $schema = new Schema();
        $schema->hydrate($node, $connection);

        return $schema;
    }
}
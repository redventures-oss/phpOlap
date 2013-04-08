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

use phpOlap\Xmla\Metadata\Schema;
use phpOlap\Xmla\Metadata\Cube;
use phpOlap\Xmla\Metadata\Hierarchy;
use phpOlap\Xmla\Metadata\Dimension;
use phpOlap\Xmla\Metadata\Level;

class SchemaTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $connection = $this->createConnection();
        $schema = $this->createSchema($connection);
        $cubes1 = $schema->getCubes();
        $cubes2 = $schema->getCubes();
        $cube1 = array_shift($cubes1);
        $cube2 = array_shift($cubes2);
		
		$this->assertEquals($schema->getConnection(), $connection);
		$this->assertEquals($schema->getName(), 'FoodMart');
		$this->assertEquals($cube1->getName(), 'cube1');
		$this->assertEquals($cube2->getName(), 'cube1');
	}

    public function testToArray()
    {
        $connection = $this->createConnection();
        $schema = $this->createSchema($connection);
        $arr = $schema->toArray();

		$this->assertEquals($arr['name'], 'FoodMart');
		$this->assertEquals($arr['cubes'][0]['name'], 'cube1');
    }

    private function createConnection()
    {
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findCubes')
					->will($this->onConsecutiveCalls(
                        array( $this->createCube($connection, 'cube1') ),
                        array( $this->createCube($connection, 'cube2') )
                    ));

        $connection->expects($this->any())
                    ->method('findHierarchies')
                    ->will($this->onConsecutiveCalls(
                        array( $this->createHierarchy($connection, 'hierarchy1') ),
                        array( $this->createHierarchy($connection, 'hierarchy2') )
                    ));

        $connection->expects($this->any())
                    ->method('findDimensions')
                    ->will($this->onConsecutiveCalls(
                        array( $this->createDimension($connection, 'dimension1') ),
                        array( $this->createDimension($connection, 'dimension2') )
                    ));

		$connection->expects($this->any())
					->method('findLevels')
					->will($this->onConsecutiveCalls(
                        array( $this->createLevel($connection, 'l1') ),
                        array( $this->createLevel($connection, 'l2') )
                    ));

        return $connection;
    }

    private function createSchema($connection)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
						<SCHEMA_NAME>FoodMart</SCHEMA_NAME>
						<SCHEMA_OWNER/>
					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);
				
		
		$schema = new Schema();
		$schema->hydrate($node, $connection);

        return $schema;
    }

    private function createCube($connection, $cubeName)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
						<SCHEMA_NAME>FoodMart</SCHEMA_NAME>
						<CUBE_NAME>' . $cubeName . '</CUBE_NAME>
						<CUBE_TYPE>CUBE</CUBE_TYPE>
						<LAST_SCHEMA_UPDATE>2011-05-07T00:52:12</LAST_SCHEMA_UPDATE>
						<IS_DRILLTHROUGH_ENABLED>true</IS_DRILLTHROUGH_ENABLED>
						<IS_WRITE_ENABLED>false</IS_WRITE_ENABLED>
						<IS_LINKABLE>false</IS_LINKABLE>
						<IS_SQL_ENABLED>false</IS_SQL_ENABLED>
						<DESCRIPTION>FoodMart Schema - Sales Cube</DESCRIPTION>
					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);

		$cube = new Cube();
		$cube->hydrate($node, $connection);

        return $cube;
    }

    private function createHierarchy($connection, $hierarchyName)
    {
		$resultSoap = '<root>
					      <row> 
					        <CATALOG_NAME>FoodMart</CATALOG_NAME> 
					        <SCHEMA_NAME>FoodMart</SCHEMA_NAME> 
					        <CUBE_NAME>Sales</CUBE_NAME> 
					        <DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME> 
					        <HIERARCHY_NAME>' . $hierarchyName . '</HIERARCHY_NAME> 
					        <HIERARCHY_UNIQUE_NAME>[Time]</HIERARCHY_UNIQUE_NAME> 
					        <HIERARCHY_CAPTION>Time</HIERARCHY_CAPTION> 
					        <DIMENSION_TYPE>1</DIMENSION_TYPE> 
					        <HIERARCHY_CARDINALITY>34</HIERARCHY_CARDINALITY> 
					        <DEFAULT_MEMBER>[Time].[1997]</DEFAULT_MEMBER> 
					        <DESCRIPTION>Sales Cube - Time Hierarchy</DESCRIPTION> 
					        <STRUCTURE>0</STRUCTURE> 
					        <IS_VIRTUAL>false</IS_VIRTUAL> 
					        <IS_READWRITE>false</IS_READWRITE> 
					        <DIMENSION_UNIQUE_SETTINGS>0</DIMENSION_UNIQUE_SETTINGS> 
					        <DIMENSION_IS_VISIBLE>true</DIMENSION_IS_VISIBLE> 
					        <HIERARCHY_ORDINAL>4</HIERARCHY_ORDINAL> 
					        <DIMENSION_IS_SHARED>true</DIMENSION_IS_SHARED> 
					        <PARENT_CHILD>false</PARENT_CHILD> 
					      </row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);
		
		$hierarchy = new Hierarchy();
		$hierarchy->hydrate($node, $connection);

        return $hierarchy;
    }

    private function createDimension($connection, $dimensionName)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
						<SCHEMA_NAME>FoodMart</SCHEMA_NAME>
						<CUBE_NAME>Sales</CUBE_NAME>
						<DIMENSION_NAME>' . $dimensionName . '</DIMENSION_NAME>
						<DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME>
						<DIMENSION_CAPTION>Time</DIMENSION_CAPTION>
						<DIMENSION_ORDINAL>4</DIMENSION_ORDINAL>
						<DIMENSION_TYPE>1</DIMENSION_TYPE>
						<DIMENSION_CARDINALITY>25</DIMENSION_CARDINALITY>
						<DEFAULT_HIERARCHY>[Time]</DEFAULT_HIERARCHY>
						<DESCRIPTION>Sales Cube - Time Dimension</DESCRIPTION>
						<IS_VIRTUAL>false</IS_VIRTUAL>
						<IS_READWRITE>false</IS_READWRITE>
						<DIMENSION_UNIQUE_SETTINGS>0</DIMENSION_UNIQUE_SETTINGS>
						<DIMENSION_IS_VISIBLE>true</DIMENSION_IS_VISIBLE>
					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);	
		
		$dimension = new Dimension();
		$dimension->hydrate($node, $connection);

        return $dimension;
    }

    private function createLevel($connection, $levelName)
    {
		$resultSoap = '<root>
			      <row> 
			        <CATALOG_NAME>FoodMart</CATALOG_NAME> 
			        <SCHEMA_NAME>FoodMart</SCHEMA_NAME> 
			        <CUBE_NAME>Sales</CUBE_NAME> 
			        <DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME> 
			        <HIERARCHY_UNIQUE_NAME>[Time.Weekly]</HIERARCHY_UNIQUE_NAME> 
			        <LEVEL_NAME>' . $levelName . '</LEVEL_NAME> 
			        <LEVEL_UNIQUE_NAME>[Time.Weekly].[Year]</LEVEL_UNIQUE_NAME> 
			        <LEVEL_CAPTION>Year</LEVEL_CAPTION> 
			        <LEVEL_NUMBER>1</LEVEL_NUMBER> 
			        <LEVEL_CARDINALITY>2</LEVEL_CARDINALITY> 
			        <LEVEL_TYPE>20</LEVEL_TYPE> 
			        <CUSTOM_ROLLUP_SETTINGS>0</CUSTOM_ROLLUP_SETTINGS> 
			        <LEVEL_UNIQUE_SETTINGS>1</LEVEL_UNIQUE_SETTINGS> 
			        <LEVEL_IS_VISIBLE>true</LEVEL_IS_VISIBLE> 
			        <DESCRIPTION>Sales Cube - Time.Weekly Hierarchy - Year Level</DESCRIPTION> 
			      </row> 
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);
		
		$level = new Level();
		$level->hydrate($node, $connection);

        return $level;
    }
}
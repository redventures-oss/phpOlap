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

use phpOlap\Xmla\Metadata\Dimension;
use phpOlap\Xmla\Metadata\Hierarchy;

class DimensionTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $connection = $this->createConnection();
        $dimension = $this->createDimension($connection);
		
        $hierarchies1 = $dimension->getHierarchies();
        $hierarchies2 = $dimension->getHierarchies();
        $hierarchy1 = array_shift($hierarchies1);
        $hierarchy2 = array_shift($hierarchies2);

		$this->assertEquals($dimension->getConnection(), $connection);
		$this->assertEquals($dimension->getName(), 'Time');
		$this->assertEquals($dimension->getUniqueName(), '[Time]');
		$this->assertEquals($dimension->getDescription(), 'Sales Cube - Time Dimension');
		$this->assertEquals($dimension->getCaption(), 'Time');
		$this->assertEquals($dimension->getOrdinal(), 4);
		$this->assertEquals($dimension->getType(), 'TIME');
		$this->assertEquals($dimension->getCardinality(), 25);
		$this->assertEquals($dimension->getDefaultHierarchyUniqueName(), '[Time]');
		$this->assertEquals($dimension->isVirtual(), false);
		$this->assertEquals($dimension->isReadWrite(), false);
		$this->assertEquals($dimension->getUniqueSettings(), 0);
		$this->assertEquals($dimension->isVisible(), true);
		$this->assertEquals($hierarchy1->getName(), 'h1');
		$this->assertEquals($hierarchy2->getName(), 'h1');
	}

    public function testToArray()
    {
        $connection = $this->createConnection();
        $dimension = $this->createDimension($connection);
        $arr = $dimension->toArray();

		$this->assertEquals($arr['name'], 'Time');
		$this->assertEquals($arr['uniqueName'], '[Time]');
		$this->assertEquals($arr['description'], 'Sales Cube - Time Dimension');
		$this->assertEquals($arr['caption'], 'Time');
		$this->assertEquals($arr['ordinal'], 4);
		$this->assertEquals($arr['type'], 'TIME');
		$this->assertEquals($arr['cardinality'], 25);
		$this->assertEquals($arr['defaultHierarchyUniqueName'], '[Time]');
		$this->assertEquals($arr['isVirtual'], false);
		$this->assertEquals($arr['isReadWrite'], false);
		$this->assertEquals($arr['uniqueSettings'], 0);
		$this->assertEquals($arr['isVisible'], true);
		$this->assertEquals($arr['hierarchies'][0]['name'], 'h1');
    }

	public function testHydrateMin()
	{
        $connection = $this->createConnection();
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
						<SCHEMA_NAME>FoodMart</SCHEMA_NAME>
						<CUBE_NAME>Sales</CUBE_NAME>
						<DIMENSION_NAME>Time</DIMENSION_NAME>
						<DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME>

					</row>
				</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);

		$node = $document->getElementsByTagName('row')->item(0);

		$dimension = new Dimension();
		$dimension->hydrate($node, $connection);
		
        $hierarchies1 = $dimension->getHierarchies();
        $hierarchies2 = $dimension->getHierarchies();
        $hierarchy1 = array_shift($hierarchies1);
        $hierarchy2 = array_shift($hierarchies2);

		$this->assertEquals($dimension->getConnection(), $connection);
		$this->assertEquals($dimension->getCubeName(), 'Sales');
		$this->assertEquals($dimension->getName(), 'Time');
		$this->assertEquals($dimension->getUniqueName(), '[Time]');
		$this->assertEquals($dimension->getDescription(), null);
		$this->assertEquals($dimension->getCaption(), null);
		$this->assertEquals($dimension->getOrdinal(), 0);
		$this->assertEquals($dimension->getType(), 'UNKNOWN');
		$this->assertEquals($dimension->getCardinality(), 0);
		$this->assertEquals($dimension->getDefaultHierarchyUniqueName(), null);
		$this->assertEquals($dimension->isVirtual(), false);
		$this->assertEquals($dimension->isReadWrite(), false);
		$this->assertEquals($dimension->getUniqueSettings(), null);
		$this->assertEquals($dimension->isVisible(), false);
		$this->assertEquals($hierarchy1->getName(), 'h1');
		$this->assertEquals($hierarchy2->getName(), 'h1');
	}

    private function createConnection()
    {
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findHierarchies')
					->will($this->onConsecutiveCalls(
                        array( $this->createHierarchy($connection, 'h1') ),
                        array( $this->createHierarchy($connection, 'h2') )
                    ));	

        return $connection;
    }

    private function createDimension($connection)
    {
		$resultSoap = '<root>
					<row>
						<CATALOG_NAME>FoodMart</CATALOG_NAME>
						<SCHEMA_NAME>FoodMart</SCHEMA_NAME>
						<CUBE_NAME>Sales</CUBE_NAME>
						<DIMENSION_NAME>Time</DIMENSION_NAME>
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
}
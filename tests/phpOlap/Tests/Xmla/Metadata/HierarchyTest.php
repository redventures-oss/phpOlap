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

use phpOlap\Xmla\Metadata\Hierarchy;
use phpOlap\Xmla\Metadata\Level;

class HierarchyTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $connection = $this->createConnection();
        $hierarchy = $this->createHierarchy($connection);
        $levels1 = $hierarchy->getLevels();
        $levels2 = $hierarchy->getLevels();
        $level1 = array_shift($levels1);
        $level2 = array_shift($levels2);
		
		$this->assertEquals($hierarchy->getConnection(), $connection);
		$this->assertEquals($hierarchy->getCubeName(), 'Sales');
		$this->assertEquals($hierarchy->getDimensionUniqueName(), '[Time]');
		$this->assertEquals($hierarchy->getName(), 'Time');
		$this->assertEquals($hierarchy->getUniqueName(), '[Time]');
		$this->assertEquals($hierarchy->getDescription(), 'Sales Cube - Time Hierarchy');
		$this->assertEquals($hierarchy->getCaption(), 'Time');
		$this->assertEquals($hierarchy->getCardinality(), 34);
		$this->assertEquals($hierarchy->getDefaultMemberUniqueName(), '[Time].[1997]');
		$this->assertEquals($hierarchy->getStructure(), 0);
		$this->assertEquals($hierarchy->isVirtual(), false);
		$this->assertEquals($hierarchy->isReadWrite(), false);
		$this->assertEquals($hierarchy->getOrdinal(), 4);
		$this->assertEquals($hierarchy->getParentChild(), false);
		$this->assertEquals($level1->getName(), 'l1');
		$this->assertEquals($level2->getName(), 'l1');

	}

    public function testToArray()
    {
        $connection = $this->createConnection();
        $hierarchy = $this->createHierarchy($connection);
        $arr = $hierarchy->toArray();

		$this->assertEquals($arr['cubeName'], 'Sales');
		$this->assertEquals($arr['dimensionUniqueName'], '[Time]');
		$this->assertEquals($arr['name'], 'Time');
		$this->assertEquals($arr['uniqueName'], '[Time]');
		$this->assertEquals($arr['description'], 'Sales Cube - Time Hierarchy');
		$this->assertEquals($arr['caption'], 'Time');
		$this->assertEquals($arr['cardinality'], 34);
		$this->assertEquals($arr['defaultMemberUniqueName'], '[Time].[1997]');
		$this->assertEquals($arr['structure'], 0);
		$this->assertEquals($arr['isVirtual'], false);
		$this->assertEquals($arr['isReadWrite'], false);
		$this->assertEquals($arr['ordinal'], 4);
		$this->assertEquals($arr['parentChild'], false);
		$this->assertEquals($arr['levels'][0]['name'], 'l1');
    }

    private function createConnection()
    {
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findLevels')
					->will($this->onConsecutiveCalls(
                        array( $this->createLevel($connection, 'l1') ),
                        array( $this->createLevel($connection, 'l2') )
                    ));

        return $connection;
    }

    private function createHierarchy($connection)
    {
		$resultSoap = '<root>
					      <row> 
					        <CATALOG_NAME>FoodMart</CATALOG_NAME> 
					        <SCHEMA_NAME>FoodMart</SCHEMA_NAME> 
					        <CUBE_NAME>Sales</CUBE_NAME> 
					        <DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME> 
					        <HIERARCHY_NAME>Time</HIERARCHY_NAME> 
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
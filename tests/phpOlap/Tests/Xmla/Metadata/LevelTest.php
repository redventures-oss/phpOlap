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

use phpOlap\Xmla\Metadata\Level;
use phpOlap\Xmla\Metadata\Member;

class LevelTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $connection = $this->createConnection();
        $level = $this->createLevel($connection);
        $members1 = $level->getMembers();
        $members2 = $level->getMembers();
        $member1 = array_shift($members1);
        $member2 = array_shift($members2);
		
		$this->assertEquals($level->getConnection(), $connection);
		$this->assertEquals($level->getCubeName(), 'Sales');
		$this->assertEquals($level->getDimensionUniqueName(), '[Time]');
		$this->assertEquals($level->getHierarchyUniqueName(), '[Time.Weekly]');
		$this->assertEquals($level->getUniqueName(), '[Time.Weekly].[Year]');
		$this->assertEquals($level->getDescription(), 'Sales Cube - Time.Weekly Hierarchy - Year Level');
		$this->assertEquals($level->getCaption(), 'Year');
		$this->assertEquals($level->getMumber(), 1);
		$this->assertEquals($level->getCardinality(), 2);
		$this->assertEquals($level->getType(), 20);
		$this->assertEquals($level->getCustomRollupSettings(), 0);
		$this->assertEquals($level->getUniqueSettings(), 1);
		$this->assertEquals($level->isVisible(), true);
		$this->assertEquals($member1->getName(), 'm1');
		$this->assertEquals($member2->getName(), 'm1');
	}

    public function testToArray()
    {
        $connection = $this->createConnection();
        $level = $this->createLevel($connection);
        $arr = $level->toArray();

		$this->assertEquals($arr['cubeName'], 'Sales');
		$this->assertEquals($arr['dimensionUniqueName'], '[Time]');
		$this->assertEquals($arr['hierarchyUniqueName'], '[Time.Weekly]');
		$this->assertEquals($arr['uniqueName'], '[Time.Weekly].[Year]');
		$this->assertEquals($arr['description'], 'Sales Cube - Time.Weekly Hierarchy - Year Level');
		$this->assertEquals($arr['caption'], 'Year');
		$this->assertEquals($arr['number'], 1);
		$this->assertEquals($arr['cardinality'], 2);
		$this->assertEquals($arr['type'], 20);
		$this->assertEquals($arr['customRollupSettings'], 0);
		$this->assertEquals($arr['uniqueSettings'], 1);
		$this->assertEquals($arr['isVisible'], true);
		$this->assertEquals($arr['members'][0]['name'], 'm1');
    }

    private function createConnection()
    {
		
		$connection = $this->getMock('phpOlap\Xmla\Connection\Connection', array(), array(), '', FALSE);
		$connection->expects($this->any())
					->method('findMembers')
					->will($this->onConsecutiveCalls(
                        array( $this->createMember($connection, 'm1') ),
                        array( $this->createMember($connection, 'm2') )
                    ));

        return $connection;
    }

    private function createLevel($connection)
    {
		$resultSoap = '<root>
			      <row> 
			        <CATALOG_NAME>FoodMart</CATALOG_NAME> 
			        <SCHEMA_NAME>FoodMart</SCHEMA_NAME> 
			        <CUBE_NAME>Sales</CUBE_NAME> 
			        <DIMENSION_UNIQUE_NAME>[Time]</DIMENSION_UNIQUE_NAME> 
			        <HIERARCHY_UNIQUE_NAME>[Time.Weekly]</HIERARCHY_UNIQUE_NAME> 
			        <LEVEL_NAME>Year</LEVEL_NAME> 
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

    private function createMember($connection, $memberName)
    {
		$resultSoap = '<root>
					      <row>
					        <CATALOG_NAME>FoodMart</CATALOG_NAME>
					        <SCHEMA_NAME>FoodMart</SCHEMA_NAME>
					        <CUBE_NAME>Sales</CUBE_NAME>
					        <DIMENSION_UNIQUE_NAME>[Measures]</DIMENSION_UNIQUE_NAME>
					        <HIERARCHY_UNIQUE_NAME>[Measures]</HIERARCHY_UNIQUE_NAME>
					        <LEVEL_UNIQUE_NAME>[Measures].[MeasuresLevel]</LEVEL_UNIQUE_NAME>
					        <LEVEL_NUMBER>0</LEVEL_NUMBER>
					        <MEMBER_ORDINAL>1</MEMBER_ORDINAL>
					        <MEMBER_NAME>' . $memberName . '</MEMBER_NAME>
					        <MEMBER_UNIQUE_NAME>[Measures].[Store Cost]</MEMBER_UNIQUE_NAME>
					        <MEMBER_TYPE>3</MEMBER_TYPE>
					        <MEMBER_CAPTION>Store Cost</MEMBER_CAPTION>
					        <CHILDREN_CARDINALITY>2</CHILDREN_CARDINALITY>
					        <PARENT_LEVEL>1</PARENT_LEVEL>
					        <PARENT_COUNT>4</PARENT_COUNT>
					        <DEPTH>10</DEPTH>
					      </row> 
						</root>';
		
		$document = new \DOMDocument();
		$document->loadXML($resultSoap);
		
		$node = $document->getElementsByTagName('row')->item(0);

		$member = new Member();
		$member->hydrate($node, $connection);

        return $member;
    }
	
}
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

use phpOlap\Xmla\Metadata\ResultSet;
use phpOlap\Xmla\Metadata\MetadataException;

class ResultSetTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $resultSet = $this->createResultSet();
		
		$this->assertEquals($resultSet->getCubeName(), 'HR');
		$this->assertEquals($resultSet->getColHierarchiesName(), array('Measures'));
		$this->assertEquals($resultSet->getRowHierarchiesName(), array('Employees'));
		$this->assertEquals($resultSet->getFilterHierarchiesName(), array('Time'));
		
		$colAxisSet = $resultSet->getColAxisSet();
		$this->assertEquals($colAxisSet[0][0]->getMemberUniqueName(), '[Measures].[Org Salary]');
		
		$rowAxisSet = $resultSet->getRowAxisSet();
		$this->assertEquals($rowAxisSet[0][0]->getMemberUniqueName(), '[Employees].[All Employees]');

		$filterAxisSet = $resultSet->getFilterAxisSet();
		$this->assertEquals($filterAxisSet[0][0]->getMemberUniqueName(), '[Time].[1997]');

		$dataSet = $resultSet->getDataSet();
		$this->assertEquals($dataSet[0]->getFormatedValue(), '$39,431.67');

		$this->assertEquals($resultSet->getDataCell(0)->getFormatedValue(), '$39,431.67');
	}

    public function testToArray()
    {
        $resultSet = $this->createResultSet();
        $arr = $resultSet->toArray();

		$this->assertEquals($arr['cubeName'], 'HR');
		$this->assertEquals($arr['colHierarchiesName'], array('Measures'));
		$this->assertEquals($arr['rowHierarchiesName'], array('Employees'));
		$this->assertEquals($arr['filterHierarchiesName'], array('Time'));

		$colAxisSet = $arr['colAxisSet'];
		$this->assertEquals($colAxisSet[0][0]['memberUniqueName'], '[Measures].[Org Salary]');
        
		$rowAxisSet = $arr['rowAxisSet'];
		$this->assertEquals($rowAxisSet[0][0]['memberUniqueName'], '[Employees].[All Employees]');

		$filterAxisSet = $arr['filterAxisSet'];
		$this->assertEquals($filterAxisSet[0][0]['memberUniqueName'], '[Time].[1997]');

		$dataSet = $arr['cellDataSet'];
		$this->assertEquals($dataSet[0]['formatedValue'], '$39,431.67');
    }

	public function testHydrateNull()
	{
        $resultSet = $this->createNullResultSet();
		
		$this->assertEquals($resultSet->getCubeName(), null);
		$this->assertEquals($resultSet->getColHierarchiesName(), null);
		$this->assertEquals($resultSet->getRowHierarchiesName(), null);
		$this->assertEquals($resultSet->getFilterHierarchiesName(), null);
		$this->assertEquals($resultSet->getColAxisSet(), null);
		$this->assertEquals($resultSet->getRowAxisSet(), null);
		$this->assertEquals($resultSet->getFilterAxisSet(), null);
	}

    public function testToArrayNull()
    {
        $resultSet = $this->createNullResultSet();
        $arr = $resultSet->toArray();

		$this->assertEquals($arr['cubeName'], null);
		$this->assertEquals($arr['colHierarchiesName'], null);
		$this->assertEquals($arr['rowHierarchiesName'], null);
		$this->assertEquals($arr['filterHierarchiesName'], null);
		$this->assertTrue(is_array($arr['colAxisSet']));
		$this->assertTrue(is_array($arr['rowAxisSet']));
		$this->assertTrue(is_array($arr['filterAxisSet']));
		$this->assertEquals(count($arr['colAxisSet']), 0);
		$this->assertEquals(count($arr['rowAxisSet']), 0);
		$this->assertEquals(count($arr['filterAxisSet']), 0);
    }

	public function testGetAttribute()
	{
		
		$document = new \DOMDocument();
		$document->loadXml('<test></test>');				
		$node = $document->getElementsByTagName('test')->item(0);

        try {
			$test = ResultSet::getAttribute($node, 'att');
        }
        catch (MetadataException $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
	}

    private function createResultSet()
    {
		
		$statementResult = new \DOMDocument();
		$statementResult->load(__DIR__.'/../Connection/statementResult.xml');
				
		$node = $statementResult->getElementsByTagName('root')->item(0);
		
		$resultSet = new ResultSet();
		$resultSet->hydrate($node);

        return $resultSet;
    }

    private function createNullResultSet()
    {
		
		$statementResult = new \DOMDocument();
		$statementResult->loadXml('<root></root>');
				
		$node = $statementResult->getElementsByTagName('root')->item(0);
		$resultSet = new ResultSet();		
		$resultSet->hydrate($node);

        return $resultSet;
    }

}
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

use phpOlap\Xmla\Metadata\CellAxis;

class CellAxisTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
		$cellAxis = $this->createCellAxis();
		
		$this->assertEquals($cellAxis->getMemberUniqueName(), '[Employees].[All Employees]');
		$this->assertEquals($cellAxis->getMemberCaption(), 'All Employees');
		$this->assertEquals($cellAxis->getLevelUniqueName(), '[Employees].[(All)]');
		$this->assertEquals($cellAxis->getLevelNumber(), 0);
		$this->assertEquals($cellAxis->getDisplayInfo(), 65537);
	}

    public function testToArray()
    {
		$cellAxis = $this->createCellAxis();
        $arr = $cellAxis->toArray();

        $this->assertTrue(is_array($arr));
    	$this->assertEquals($arr['memberUniqueName'], '[Employees].[All Employees]');
		$this->assertEquals($arr['memberCaption'], 'All Employees');
		$this->assertEquals($arr['levelUniqueName'], '[Employees].[(All)]');
		$this->assertEquals($arr['levelNumber'], 0);
		$this->assertEquals($arr['displayInfo'], 65537);
    }

    private function createCellAxis()
    {
		$axisXml = new \DOMDocument();
		$axisXml->loadXML('
			<Member Hierarchy="Employees">
                <UName>[Employees].[All Employees]</UName>
                <Caption>All Employees</Caption>
                <LName>[Employees].[(All)]</LName>
                <LNum>0</LNum>
                <DisplayInfo>65537</DisplayInfo>
              </Member>');
				
		$node = $axisXml->getElementsByTagName('Member')->item(0);
		
		$cellAxis = new CellAxis();
		$cellAxis->hydrate($node);

        return $cellAxis;
    }
}
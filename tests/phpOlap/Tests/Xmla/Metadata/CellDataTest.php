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

use phpOlap\Xmla\Metadata\CellData;

class CellDataTest extends \PHPUnit_Framework_TestCase
{

	public function testHydrate()
	{
        $cell = $this->createCell();

		$this->assertEquals($cell->getValue(), 39431.6712);
		$this->assertEquals($cell->getFormatedValue(), '$39,431.67');
		$this->assertEquals($cell->getFormatString(), 'Currency');
	}

    public function testToArray()
    {
        $cell = $this->createCell();
        $arr = $cell->toArray();

		$this->assertEquals($arr['value'], 39431.6712);
		$this->assertEquals($arr['formatedValue'], '$39,431.67');
		$this->assertEquals($arr['formatString'], 'Currency');
    }

    private function createCell()
    {
		
		$cellXml = new \DOMDocument();
		$cellXml->loadXML('
	        <Cell CellOrdinal="0">
	          <Value>39431.6712</Value>
	          <FmtValue>$39,431.67</FmtValue>
	          <FormatString>Currency</FormatString>
	        </Cell>');
				
		$node = $cellXml->getElementsByTagName('Cell')->item(0);
		
		$cell = new CellData();
		$cell->hydrate($node);

        return $cell;
    }
}
<?php

namespace phpOlap\Layout\Json;

use phpOlap\Metadata\ResultSetInterface;
use phpOlap\Layout\LayoutInterface;

/**
 * @author Ryan Fink <rfink@redventures.net>
 * @package Layout
 * @subpackage Json
 */
class FlatJson implements LayoutInterface
{
    /**
     * Result set from query
     *
     * @var ResultSetInterface
     */
    private $resultSet;
    
    /**
     * Constructor
     *
     * @param ResultSetInterface $resultSet
     */
    public function __construct(ResultSetInterface $resultSet)
    {
        $this->resultSet = $resultSet;
    }
    
    /**
     * Traverse our result set and return a flat json-like structure (actually php nested arrays)
     *   Can call json_encode directly, will show up correctly
     *
     * @return array
     */
    public function generate()
    {
        $return = array();
        $return['header'] = $this->generateHeader();
        $return['body'] = $this->generateBody();
        
        return $return;
    }
    
    /**
     * Generate the header of the structure and return
     *
     * @return array
     */
    public function generateHeader()
    {
        $header = array(
            'rows' => array(),
            'columns' => array()
        );
        foreach ($this->resultSet->getColAxisSet() as $row => $colHierarchyName) {
            $header['rows'][] = array(
                'memberUniqueName' => $colHierarchyName[0]->getMemberUniqueName(),
                'memberCaption' => $colHierarchyName[0]->getMemberCaption()
            );
        }
        $rowAxisSet = $this->resultSet->getRowAxisSet();
        $rowAxisDefinition = reset($rowAxisSet);
        foreach ($rowAxisDefinition as $col => $rowHierarchyName) {
            $mdxName = $rowHierarchyName->getLevelUniqueName();
            $nameHierarchy = explode('.', str_replace(array('[', ']'), '', $mdxName));
            $caption = end($nameHierarchy);
            $header['columns'][] = array(
                'memberUniqueName' => $rowHierarchyName->getLevelUniqueName(),
                'memberCaption' => $caption
            );
        }
        $header['headers'] = array_merge($header['columns'], $header['rows']);

        return $header;
    }
    
    /**
     * Generate the body of the structure and return
     *
     * @return array
     */
    public function generateBody()
    {
		$body = array();
		$rowAxisSet = $this->resultSet->getRowAxisSet();
		$dataSet = $this->resultSet->getDataSet();
        $columnAxisSet = $this->resultSet->getColAxisSet();
        $columnAxisCount = count($columnAxisSet);
        foreach ($rowAxisSet as $rowNumber => $aCol) {
            $row = array();
            $start = $columnAxisCount * $rowNumber;
            $stop = $start + $columnAxisCount;
            foreach ($aCol as $key => $columnDefinition)
            {
                $row[$columnDefinition->getLevelUniqueName()] = array(
                    'memberUniqueName' => $columnDefinition->getMemberUniqueName(),
                    'memberCaption' => $columnDefinition->getMemberCaption()
                );
            }
            $counter = 0;
            for ($i = $start; $i < $stop; ++$i) {
                /* TODO: break out into renderCellData method */
                if (isset($dataSet[$i])) {
                    $columnName = $columnAxisSet[$counter][0]->getMemberUniqueName();
                    $row[$columnName] = array(
                        'label' => $columnAxisSet[$counter][0]->getMemberCaption(),
                        'value' => $dataSet[$i]->getValue(),
                        'formatedValue' => $dataSet[$i]->getFormatedValue()
                    );
                }
                ++$counter;
            }
            $body[] = $row;
        }

        return $body;
    }
}

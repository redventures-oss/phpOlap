<?php 

/*
* This file is part of phpOlap.
*
* (c) Julien Jacottet <jjacottet@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace phpOlap\Xmla\Metadata;

use phpOlap\Xmla\Connection\ConnectionInterface;
use phpOlap\Xmla\Metadata\MetadataBase;
use phpOlap\Metadata\SchemaInterface;
use phpOlap\Metadata\SerializeMetadataInterface;

/**
*	Schema class
*
*  	@author Julien Jacottet <jjacottet@gmail.com>
*	@package Xmla
*	@subpackage Metadata
*/
class Schema extends MetadataBase implements SchemaInterface, SerializeMetadataInterface
{
	protected $cubes;
	protected $description = null;
	protected $schemaOwner;


    /**
     * Get cubes
     *
     * @return Array Cubes collection
     *
     */
	public function getCubes()
	{
		if (!$this->cubes) {
			$this->cubes = $this->getConnection()->findCubes(
				array(),
				array('SCHEMA_NAME' => $this->getName())
			);
		}
		return $this->cubes;
	}

    /**
     * Get unique name
     *
     * @return String Unique name
     *
     */
	public function getUniqueName(){
		return "[" . $this->name . "]";
	}

    /**
     * Hydrate Element
     *
     * @param DOMNode $node Node
     * @param Connection $connection Connection
     *
     */	
	public function hydrate(\DOMNode $node,ConnectionInterface $connection)
	{
		$this->connection = $connection;
		$this->name = parent::getPropertyFromNode($node, 'SCHEMA_NAME', false);
	}

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $cubes = array();
        
        foreach ((array) $this->getCubes() as $cube) {
            $cubes[] = $cube->toArray();
        }

        return array(
            'name' => $this->getName(),
            'uniqueName' => $this->getUniqueName(),
            'description' => $this->getDescription(),
            'cubes' => $cubes
        );
    }
}
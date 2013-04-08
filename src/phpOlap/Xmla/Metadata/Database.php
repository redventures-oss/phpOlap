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
use phpOlap\Metadata\DatabaseInterface;
use phpOlap\Metadata\SerializeMetadataInterface;

/**
*	Database class
*
*  	@author Julien Jacottet <jjacottet@gmail.com>
*	@package Xmla
*	@subpackage Metadata
*/
class Database extends MetadataBase implements DatabaseInterface, SerializeMetadataInterface
{
	protected $url;
	protected $dataSourceInfo;
	protected $providerName;
	protected $providerType;
	protected $authenticationMode;
	protected $catalogs;

    /**
     * Get url
     *
     * @return String url
     *
     */
	public function getUrl(){
		return $this->url;
	}

    /**
     * Get data source info
     *
     * @return String dataSourceInfo
     *
     */
	public function getDataSourceInfo(){
		return $this->dataSourceInfo;
	}
	
    /**
     * Get provider name
     *
     * @return String provider name
     *
     */
	public function getProviderName(){
		return $this->providerName;
	}

    /**
     * Get provider type
     *
     * @return String provider type
     *
     */
	public function getProviderType(){
		return $this->providerType;
	}

    /**
     * Get authentication mode
     *
     * @return String authentication mode
     *
     */
	public function getAuthenticationMode(){
		return $this->authenticationMode;
	}
	
    /**
     * Get catalogs
     *
     * @return Array Catalogs collection
     *
     */
	public function getCatalogs()
	{
		if (!$this->catalogs) {
			$this->catalogs = $this->getConnection()->findCatalogs(
					array('DataSourceInfo' => $this->getDataSourceInfo())
				);
		}
		return $this->catalogs;
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
		$this->name = parent::getPropertyFromNode($node, 'DataSourceName', false);
		$this->description = parent::getPropertyFromNode($node, 'DataSourceDescription');
		$this->url = parent::getPropertyFromNode($node, 'URL');
		$this->dataSourceInfo = parent::getPropertyFromNode($node, 'DataSourceInfo', false);
		$this->providerName = parent::getPropertyFromNode($node, 'ProviderName');
		$this->providerType = parent::getPropertyFromNode($node, 'ProviderType');
		$this->authenticationMode = parent::getPropertyFromNode($node, 'AuthenticationMode');
	}

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $catalogs = array();

        foreach ((array) $this->getCatalogs() as $catalog) {
            $catalogs[] = $catalog->toArray();
        }

        return array(
            'name' => $this->getName(),
            'uniqueName' => $this->getUniqueName(),
            'description' => $this->getDescription(),
            'url' => $this->getUrl(),
            'dataSourceInfo' => $this->getDataSourceInfo(),
            'providerName' => $this->getProviderName(),
            'providerType' => $this->getProviderType(),
            'authenticationMode' => $this->getAuthenticationMode(),
            'catalogs' => $catalogs
        );
    }

}
<?php

namespace phpOlap\Metadata;

/**
 * Interface defining methods to serialize our metadata object.
 *
 * @author Ryan Fink <ryanjfink@gmail.com>
 * @since  April 5, 2013
 */
interface SerializeMetadataInterface
{
    /**
     * Serialize our metadata object to an array
     *
     * @return array
     */
    public function toArray();
}

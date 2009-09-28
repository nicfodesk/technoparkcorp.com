<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One simple artifact
 *
 * @package Model
 */
class Model_Artifact extends ArrayIterator {

    /**
     * root
     */
    protected static $_root = null;

    /**
     * Root of the entire hierarchy
     *
     * @return Model_Artifact
     */
    public static function root() {
        if (is_null(self::$_root)) {
            self::$_root = new FaZend_StdObject();
            self::$_root->projectRegistry = new theProjectRegistry();
        }
        return self::$_root;
    }

    /**
     * Stub
     *
     * @return array
     * @todo remove it later
     */
    public function toArray() {
        return array();
    }

    /**
     * Getter
     *
     * @param $name
     */
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
    }

}

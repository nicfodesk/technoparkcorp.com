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
 * One Quality Controller wobot
 *
 * @package Model
 */
class Model_Wobot_QC extends Model_Wobot {

    /**
     * Returns a list of all possible wobot names of this given type/class
     *
     * @return string[]
     **/
    public static function getAllNames() {
        $names = array();
        foreach (Model_Process::retrieveAll() as $process);
            $names[] = 'QC.' . strval($process);
        return $names;
    }

    /**
     * Process
     *
     * @var Model_Process
     */
    protected $_process;

    /**
     * Initializer
     *
     * @param string Name of the process
     * @return void
     */
    protected function __construct($context = null) {
        $this->_process = Model_Process::findByName($context);
    }

    /**
     * Calculate context
     *
     * @return string
     */
    public function getContext() {
        return strval($this->_process);
    }

    /**
     * Get the full name of the human-wobot
     *
     * @return string
     */
    public function getHumanName() {
        return 'Mr. Quality';
    }

}

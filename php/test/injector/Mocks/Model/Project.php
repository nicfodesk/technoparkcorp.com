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
 * One project for test
 *
 * @package Model
 */
class mock_Model_Project extends Model_Project {

    const NAME = 'test';
    const OWNER = 'yegor256@yahoo.com';
    const OWNER_PWD = 'violetta';
    const PM = 'tester@tpc2.com';
    const PM_PWD = 'violetta';

    /**
     * Instance of project
     *
     * @var Model_Project_Test
     */
    protected static $_instance;

    /**
     * Create test project class
     *
     * @return void
     **/
    public function __construct() {
        $pwd = md5(rand());
        
        $authz = '[' . self::NAME . ":/]\n" . self::PM . " = rw\n";
        foreach (array('SystemAnalyst', 'Architect', 'CCB') as $role)
            $authz .= '[' . self::NAME . ':' . Model_Project::ROLE_AUTHZ_PREFIX . "$role]\n" . self::PM . " = rw\n";
        
        parent::__construct(1, // id
            self::NAME, // project name
            new Shared_User(1, self::OWNER, self::OWNER_PWD), // project owner
            $authz, // authz file
                self::PM . ' = ' . self::PM_PWD . "\n" . 
                self::OWNER . '=' . self::OWNER_PWD . "\n" // passwd file
            );     
    }

    /**
     * Create and return a test project instance
     *
     * @return mock_Project
     **/
    public static function getInstance() {
        if (!isset(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

}
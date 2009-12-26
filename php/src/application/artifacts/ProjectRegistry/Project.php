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
 * One project in the project registry
 *
 * @package Artifacts
 */
class theProject extends Model_Artifact implements Model_Artifact_Passive 
{
    
    /**
     * Initialize it
     *
     * @return void
     **/
    public function init() 
    {
        parent::init();

        // we don't need to keep versions in this artifact
        $this->ps()->setIgnoreVersions();
    }
    
    /**
     * Initialize project
     * 
     * @return void
     */
    public function reload() 
    {
        $this->_passiveLoader()->reload();
    }
    
    /**
     * Is it current?
     * 
     * @return boolean
     */
    public function isLoaded() 
    {
        return $this->_passiveLoader()->isLoaded();
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
            
        return parent::__get($name);
    }

    /**
     * Get project from fazend
     * 
     * @return Model_Project
     */
    public function fzProject() 
    {
        return Model_Project::findByName($this->name);
    }
    
    /**
     * Show project as a string
     *
     * @return string
     **/
    public function __toString() 
    {
        return $this->name;
    }

    /**
     * Get name of the project
     *
     * @return string
     **/
    protected function _getName() 
    {
        return $this->ps()->name;
    }
    
    /**
     * Create class loader
     *
     * The method creates a standalone loader, which is responsible
     * for adding elements to $this. Also this loader knows how to
     * understand whether $this is loaded now or not.
     *
     * @return Model_Artifact_Passive_Loader
     * @see isLoaded()
     * @see reload()
     **/
    protected function _passiveLoader() 
    {
        return Model_Artifact_Passive_Loader::factory($this)
            ->attach('staffAssignments', new theStaffAssignments(), 'project')
            ->attach('workOrders', new theWorkOrders(), 'project')
            ->attach('milestones', new theMilestones())
            ->attach('objectives', new theObjectives())
            ->attach('traceability', new theTraceability())
            ->attach('deliverables', new theDeliverables())
            ->attach('payments', new thePayments(), 'project')
            ->attach('metrics', new theMetrics())
            ->attach('wbs', new theWbs())
            ->attach('activityList', new theActivityList())
            ->attach('schedule', new theSchedule())
            ;
    }
    
}

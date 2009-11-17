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
 * One request for staff
 *
 * @package Artifacts
 */
class theStaffRequest {

    /**
     * Unique ID of the staff request
     *
     * @var string
     */
    protected $_id;

    /**
     * Project
     *
     * @var theProject
     */
    protected $_project;
    
    /**
     * Role in the project
     *
     * @var theProjectRole
     */
    protected $_role;
    
    /**
     * List of skills required
     *
     * @var theSupplierSkills
     */
    protected $_skills;
    
    /**
     * List of activities
     *
     * @var theActivity[]
     */
    protected $_activities = array();
    
    /**
     * Quality threshold we can accept
     *
     * @var integer
     */
    protected $_threshold = 75;

    /**
     * Construct the class
     *
     * @param string Unique ID of the request
     * @return void
     */
    public function __construct($id) {
        $this->_id = $id;
        $this->_skills = new theSupplierSkills();
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));
    }
    
    /**
     * Set project
     *
     * @param theProject Project which requires staff
     * @return void
     **/
    public function setProject(theProject $project) {
        $this->_project = $project;
        return $this;
    }

    /**
     * Set role
     *
     * @param theProjectRole Project which requires staff
     * @return void
     **/
    public function setRole(theProjectRole $role) {
        $this->_role = $role;
        return $this;
    }

    /**
     * Set threshold
     *
     * @param integer Threshold
     * @return void
     **/
    public function setThreshold($threshold) {
        validate()
            ->type($threshold, 'integer', "Threshold must be INTEGER")
            ->true($threshold <= 100 && $threshold >= 0, "Threshold must be in [0..100] interval, {$threshold} provided");
        $this->_threshold = $threshold;
        return $this;
    }

    /**
     * Add skill
     *
     * @param theSupplierSkill Skill required, with grade
     * @return void
     **/
    public function addSkill(theSupplierSkill $skill) {
        $this->_skills[] = $skill;
        return $this;
    }

    /**
     * Add activity
     *
     * @param theActivity Activity that is planned for this person
     * @return void
     **/
    public function addActivity(theActivity $activity) {
        $this->_activity[] = $activity;
        return $this;
    }
    
    /**
     * Total duration in days of this request
     *
     * @return integer
     **/
    protected function _getDuration() {
        $duration = 0;
        foreach ($this->_activities as $activity)
            $duration += $activity->duration;
        return $duration;
    }

    /**
     * Total cost of this request
     *
     * @return Model_Cost
     **/
    protected function _getCost() {
        $cost = new Model_Cost();
        foreach ($this->_activities as $activity)
            $cost->add($activity->cost);
        return $cost;
    }

    /**
     * Response
     *
     * @return theStaffResponse
     **/
    protected function _getResponse() {
        $response = new theStaffResponse();
        foreach (Model_Artifact::root()->supplierRegistry as $supplier) {
            if (!$supplier->roles->hasRole($this->role))
                continue;
            
            // start logging everything that happens later
            FaZend_Log::getInstance()->addWriter('Memory', 'staffResponse');

            $qualities = array();
            foreach ($this->skills as $skill) {
                if (!$supplier->skills->hasSkill($skill)) {
                    logg("Skill '{$skill}' is absent");
                    $qualities[] = 0;
                    continue;
                }
                $compliance = $supplier->skills->getCompliance($skill);
                $qualities[] = $compliance;
                logg("Compliance to skill '{$skill}' is {$compliance}%");
            }

            $item = new theStaffResponseItem();
            $item->setSupplier($supplier)
                ->setQuality(intval(array_sum($qualities) / count($qualities)))
                ->setReason(FaZend_Log::getInstance()->getWriterAndRemove('staffResponse')->getLog());
            $response[] = $item;
        }
        return $response;
    }
    
}
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
 * Collection of suppliers
 *
 * @package Artifacts
 */
class theSupplierRegistry extends Model_Artifact_Bag 
    implements ArrayAccess, Iterator, Countable, Model_Artifact_Passive, Model_Artifact_Interface
{

    /**
     * List of suppliers emails
     *
     * @var ArrayIterator
     **/
    protected $_suppliers;

    /**
     * Construct the class
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // we don't need to keep versions in this artifact
        $this->ps()->setIgnoreVersions();
        
        $this->_suppliers = new ArrayIterator();
    }

    /**
     * The list is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() 
    {
        return count($this->_suppliers) > 0;
    }

    /**
     * Reload list of suppliers
     *
     * @return array
     **/
    public function reload() 
    {
        $asset = Model_Project::findByName('PMO')->getAsset(Model_Project::ASSET_SUPPLIERS);
        foreach ($asset->retrieveAll() as $email)
            $this->_suppliers[$email] = false;
    }
    
    /**
     * Resolve one staff request
     *
     * @param theStaffRequest
     * @return theStaffResponse
     **/
    public function resolve(theStaffRequest $request) 
    {
        $response = new theStaffResponse();
        foreach ($this as $supplier) {
            if (!$supplier->hasRole($request->role))
                continue;
            
            // start logging everything that happens later
            FaZend_Log::getInstance()->addWriter('Memory', 'staffResponse');

            $qualities = array();
            foreach ($request->skills as $skill=>$grade) {
                if (!$supplier->hasSkill($skill)) {
                    logg("Skill '{$skill}' is absent");
                    $qualities[] = 0;
                    continue;
                }
                $compliance = $supplier->getCompliance($skill);
                $qualities[] = $compliance;
                logg("Compliance to skill '{$skill}' is {$compliance}%");
            }

            $item = new theStaffResponseItem();
            $item->setSupplier($supplier)
                ->setQuality(count($qualities) ? intval(array_sum($qualities) / count($qualities)) : 100)
                ->setReason(FaZend_Log::getInstance()->getWriterAndRemove('staffResponse')->getLog());
            $response[] = $item;
        }
        return $response;
    }
    
    /**
     * Supplier exists?
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Name of the statement (email)
     * @return boolean
     */
    public function offsetExists($email) 
    {
        $this->_suppliers->offsetExists($email);
    }

    /**
     * Get one supplier
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Supplier's email
     * @return theSupplier
     */
    public function offsetGet($email) 
    {
        $suppliers = $this->_suppliers;
        if (!isset($suppliers[$email]))
            FaZend_Exception::raise('SupplierRegistryNotFound', 
                "Supplier '{$email}' not found in list (" . count($suppliers) . ' total)');
        
        if ($suppliers[$email] === false) {
            $supplier = new theSupplier($email);
            $asset = Model_Project::findByName('PMO')->getAsset(Model_Project::ASSET_SUPPLIERS);
            
            $asset->deriveByEmail($email, $supplier);
            $suppliers[$email] = $supplier;
        }

        return $suppliers[$email];
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     */
    public function offsetSet($email, $value) 
    {
        FaZend_Exception::raise('SupplierRegistryException', "Suppliers are not editable directly");
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     */
    public function offsetUnset($email) 
    {
        FaZend_Exception::raise('SupplierRegistryException', "Suppliers are not editable directly");
    }

    /**
     * Return current element
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theSupplier
     */
    public function current() 
    {
        return $this->offsetGet($this->key());
    }
    
    /**
     * Return next
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theSupplier
     */
    public function next() 
    {
        $this->_suppliers->next();
        $key = $this->key();
        if ($key)
            return $this->offsetGet($key);
        return false;
    }
    
    /**
     * Return key
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theSupplier
     */
    public function key() 
    {
        return $this->_suppliers->key();
    }
    
    /**
     * Is valid?
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return boolean
     */
    public function valid() 
    {
        return $this->_suppliers->valid();
    }
    
    /**
     * Rewind
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return void
     */
    public function rewind() 
    {
        return $this->_suppliers->rewind();
    }
    
    /**
     * Count them
     * 
     * The method is required by Countable interface, don't delete it.
     *
     * @return integer
     */
    public function count() 
    {
        return $this->_suppliers->count();
    }
    
}

<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Collection of suppliers
 *
 * You can work with it as with an associative array. Keys are emails
 * of suppliers, and values are instances of {@link theSupplier} class.
 *
 * @package Artifacts
 * @property _suppliers Holds a collection of suppliers
 */
class theSupplierRegistry extends Model_Artifact_Bag 
    implements ArrayAccess, Iterator, Countable, Model_Artifact_Passive, Model_Artifact_Interface
{

    /**
     * The list is loaded? It is always loaded, meaning that only explicit reloading may reload it
     *
     * @return true
     */
    public function isLoaded() 
    {
        return true;
    }

    /**
     * Reload list of suppliers
     *
     * @return void
     */
    public function reload() 
    {
        $this->_suppliers = new ArrayIterator();
        $suppliers = $this->_getSuppliers();
        $asset = Model_Project::findByName('PMO')->getAsset(Model_Project::ASSET_SUPPLIERS);
        foreach ($asset->retrieveAll() as $email) {
            $suppliers[$email] = false;
        }
        $suppliers->rewind();
    }
    
    /**
     * Resolve one staff request
     *
     * @param theStaffRequest
     * @return theStaffResponse
     */
    public function resolve(theStaffRequest $request) 
    {
        $response = new theStaffResponse();
        foreach ($this as $supplier) {
            if (!$supplier->hasRole($request->role)) {
                continue;
            }
            
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
     * Retrive full list of suppliers approved in the specified interval
     *
     * @return theSupplier[]
     */
    public function retrieveApprovedByInterval($start, $end) 
    {
        $list = new ArrayIterator();
        foreach ($this as $supplier) {
            if ($supplier->approvedOn->isEarlier($end) && $supplier->approvedOn->isLater($start)) {
                $list[] = $supplier;
            }
        }
        return $list;
    }
    
    /**
     * Returns a list of reports for PMO
     *
     * @return array
     */
    public function getPmoReports() 
    {
        $reports = new ArrayIterator();
        for ($i=0; $i<5; $i++) {
            $date = Zend_Date::now()->sub($i, Zend_Date::MONTH);
            $reports[] = FaZend_StdObject::create()
                ->set('id', $date->get(Zend_Date::MONTH . '-' . Zend_Date::YEAR))
                ->set('name', $date->get(Zend_Date::MONTH_NAME . ' ' . Zend_Date::YEAR));
        }
        return $reports;
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
        return $this->_getSuppliers()->offsetExists($email);
    }

    /**
     * Get one supplier
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Supplier's email
     * @return theSupplier
     * @see reload()
     */
    public function offsetGet($email) 
    {
        $suppliers = $this->_getSuppliers();
        if (!isset($suppliers[$email])) {
            FaZend_Exception::raise(
                'SupplierRegistryNotFound', 
                "Supplier '{$email}' not found in list (" . count($suppliers) . ' total)'
            );
        }
        
        if ($suppliers[$email] === false) {
            $supplier = new theSupplier($email);
            $asset = Model_Project::findByName('PMO')->getAsset(Model_Project::ASSET_SUPPLIERS);
            
            $asset->deriveByEmail($email, $supplier);
            $suppliers[$email] = $supplier;
            
            // make sure the registry is dirty now and will be saved to POS
            $this->ps()->setDirty();
        }

        return $suppliers[$email];
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws SupplierRegistryException
     */
    public function offsetSet($email, $value) 
    {
        FaZend_Exception::raise(
            'SupplierRegistryException', 
            "Suppliers are not editable directly"
        );
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws SupplierRegistryException
     */
    public function offsetUnset($email) 
    {
        FaZend_Exception::raise(
            'SupplierRegistryException', 
            "Suppliers are not editable directly"
        );
    }

    /**
     * Return current element
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * Lazy-loading mechanism is implemented here. We have only keys (as
     * emails) and not real-life objects.
     *
     * @return theSupplier
     * @see reload()
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
     * The method is intentionally designed like this, in order to implement
     * lazy-loading of suppliers in the array. When reload() creates an array
     * of suppliers - it sets FALSE to all of them. And later we can build them
     * using array keys as their emails. This lazy-loading mechanism also
     * affects current() method.
     *
     * @return theSupplier
     * @see reload()
     * @see current()
     */
    public function next() 
    {
        $this->_getSuppliers()->next();
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
        return $this->_getSuppliers()->key();
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
        return $this->_getSuppliers()->valid();
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
        $this->_getSuppliers()->rewind();
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
        return $this->_getSuppliers()->count();
    }

    /**
     * Get a list of suppliers, internal holder
     *
     * @return theSupplier[]
     */
    protected function _getSuppliers() 
    {
        if (!isset($this->_suppliers)) {
            $this->_suppliers = new ArrayIterator();
        }
        return $this->_suppliers;
    }
    
}

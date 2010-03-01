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
 * Traceability between deliverables
 *
 * @package Artifacts
 */
class theTraceability extends Model_Artifact_Bag
{
    
    /**
     * Clean all traceability links
     *
     * @return void
     **/
    public function clean()
    {
        // remove all items from the array
        $this->ps()->cleanArray();
    }
    
    /**
     * Add new traceability link
     *
     * @param theTraceabilityLink The element to add
     * @return $this
     **/
    public function add(theTraceabilityLink $link)
    {
        $this[] = $link;
        // logg("Traceability link added: {$link}");
        return $this;
    }
    
    /**
     * Get list of all known source types
     *
     * @return string[]
     **/
    public function getAllSourceTypes() 
    {
        $types = array();
        foreach ($this as $link) {
            $types[$link->fromType] = true;
        }
        return array_keys($types);
    }
     
    /**
     * Get list of all known destination types
     *
     * @return string[]
     **/
    public function getAllDestinationTypes() 
    {
        $types = array();
        foreach ($this as $link) {
            $types[$link->toType] = true;
        }
        return array_keys($types);
    }
     
    /**
     * Get list of deliverables, by given type 
     *
     * @param string Type, e.g. "interface" or "method"
     * @return Deliverables_Abstract
     **/
    public function getSourcesByType($type) 
    {
        $deliverables = array();
        foreach ($this as $link) {
            if ($link->fromType == $type) {
                $deliverables[$link->fromName] = $this->ps()->parent->deliverables[$link->fromName];
            }
        }
        return $deliverables;
    }
     
    /**
     * Get full list of links by the given source of traceability
     *
     * @param string|Deliverables_Abstract Source of traceability
     * @return theTraceabilityLink[]
     **/
    public function getLinksBySource($source) 
    {
        $this->_normalize($source);
        $links = array();
        foreach ($this as $link) {
            if ($link->isFrom($source)) {
                $links[] = $link;
            }
        }
        return $links;
    }

    /**
     * Get full list of links by the given destination
     *
     * @param string|Deliverables_Abstract Destination of traceability
     * @return theTraceabilityLink[]
     **/
    public function getLinksByDestination($dest) 
    {
        $this->_normalize($dest);
        $links = array();
        foreach ($this as $link) {
            if ($link->isTo($dest)) {
                $links[] = $link;
            }
        }
        return $links;
    }

    /**
     * Calculate average deep
     *
     * @param string|array Name of deliverable or name of class who should be covered
     * @param string|array Name of deliverable or name of class who should cover
     * @return float
     **/
    public function getDeep($froms, $tos)
    {
        // todo
    }
     
    /**
     * Returns indexes of elements in $to, which are covered by any elements from $from
     *
     * Returned array is an associative array, where keys are names KEYS from
     * array $to and values are numbers of elements from $from that cover
     * said element from $to.
     *
     * @param string|array Name of deliverable or name of class who should cover
     * @param string|array Name of deliverable or name of class who should be covered
     * @return array()
     */
    public function getCoverageMatrix($from, $to) 
    {
        $fromTags = $this->_getNormalizedTags($from);
        $toTags = $this->_getNormalizedTags($to);

        $covered = array_map(create_function('', 'return false;'), $toTags);
        foreach ($this as $link) {
            if (in_array($link->to, $toTags) && in_array($link->from, $fromTags)) {
                $covered[array_search($link->to, $toTags)] += 1;
            }
        }

        return $covered;
    }
     
    /**
     * Returns deliverables that are sources in this direction
     *
     * @param string|array Name of deliverable or name of class who should cover
     * @param string|array Name of deliverable or name of class who should be covered
     * @return Deliverables_Abstract[]
     */
    public function getCoverageSources($from, $to) 
    {
        $fromTags = $this->_getNormalizedTags($from);
        $toTags = $this->_getNormalizedTags($to);

        $sources = array();
        foreach ($this as $link) {
            if (in_array($link->to, $toTags) && in_array($link->from, $fromTags)) {
                $sources[array_search($link->from, $fromTags)] = $this->ps()->parent->deliverables[$link->fromName];
            }
        }
        return $sources;
    }
     
    /**
     * Returns traceability chains for every deliverable, until it reaches destination
     *
     * The array returned is an associative array, where keys are
     * names of deliverables from $from, and values are arrays of arrays. Every
     * array there is a list of
     * deliverables that are found in the chain from this deliverable,
     * when it is traceable to $to.
     *
     * If a chain is empty, empty array will be the value of array item.
     *
     * @param string|array Name of deliverable or name of class who should cover
     * @param string|array Name of deliverable or name of class who should be covered
     * @return array(Deliverables_Abstract[])
     * @see Model_Algo_Cpm_Paths
     */
    public function getCoverageChains($from, $to) 
    {
        $algo = Model_Algo::factory(
            'pathFinder', 
            array(
                'pairs' => $this,
            )
        );
    
        $toTags = $this->_getNormalizedTags($to);
        $this->_normalize($from);
        $chains = array();
        foreach ($from as $source) {
            $paths = array();
            foreach ($toTags as $dest) {
                $chain = $algo->find(theTraceabilityLink::getDeliverableTag($source), $dest);
                if ($chain) {
                    $paths[$dest] = $chain;
                }
            }
            if ($paths) {
                $chains[$source->name] = $paths;
            }
        }
        return $chains;
    }
     
    /**
     * Calculate coverage
     *
     * @param string|array Name of deliverable or name of class who should cover
     * @param string|array Name of deliverable or name of class who should be covered
     * @return float
     **/
    public function getCoverage($from, $to)
    {
        $this->_normalize($from);
        $this->_normalize($to);

        // to avoid division by zero
        if (!count($to)) {
            return 0;
        }
        
        $covered = array_filter($this->getCoverageMatrix($from, $to));
        return count($covered) / count($to);
    }
    
    /**
     * Normalize the param and make sure it looks like an array of Deliverables
     *
     * No matter what you provide, result array will contain individual 
     * deliverables, instances of class Deliverables_Abstract.
     *
     * @param string|Deliverables_Abstract|array
     * @return void
     * @throws Traceability_UnknownTypeOfArgumentException
     * @see _getNormalizedTags()
     */
    protected function _normalize(&$smth) 
    {
        // make sure it's array in any case
        if (!is_array($smth) && !($smth instanceof ArrayAccess)) {
            $smth = array($smth);
        }
        
        foreach ($smth as $id=>&$deliverable) {
            // is is OK already?
            if ($deliverable instanceof Deliverables_Abstract) {
                continue;
            }
            
            // otherwises it should be a string
            if (!is_string($deliverable)) {
                FaZend_Exception::raise(
                    'Traceability_UnknownTypeOfArgumentException', 
                    "What does this deliverable mean: '{$deliverable}'?"
                );        
            }
            
            // it's a deliverable name?
            if (isset($this->ps()->parent->deliverables[$deliverable])) {
                $deliverable = $this->ps()->parent->deliverables[$deliverable];
                continue;
            }

            // otherwise it's an aggregator
            foreach ($this->ps()->parent->deliverables->$deliverable as $found) {
                $smth[] = $found;
            }
            unset($smth[$id]);
        }
    }
    
    /**
     * Get list of traceability tags
     *
     * No matter what is provided the result array will contain
     * traceability tags, like "class:Model_User", "issue:#564", etc.
     *
     * @param string Source, like "design", "useCases", or "R4.3"
     * @return string[]
     */
    protected function _getNormalizedTags(&$list) 
    {
        $this->_normalize($list);
        
        $tags = array();
        foreach ($list as $deliverable) {
            $tags[] = theTraceabilityLink::getDeliverableTag($deliverable);
        }
        return $tags;
    }
    
}

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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Total number of actors
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Requirements_Actors_Total extends Metric_Abstract
{

    const PORTION = 0.12;

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        // we can't calculate metrics here if deliverables are not loaded
        if (!$this->_project->deliverables->isLoaded()) {
            $this->_project->deliverables->reload();
        }
            
        $this->value = count($this->_project->deliverables->actors);
    }
        
    /**
     * Split the WP onto activities
     *
     * @param Slice_Plugin_Abstract
     * @return void
     * @deprecated Delete it!
     **/
    protected function _split(Slice_Plugin_Abstract $slice)
    {
        // split the activity onto smaller pieces
        $total = $slice->iterate(
            'downCurve', 
            array(
                'sow' => 'Identify actors',
                'minCost' => '10 USD'
            )
        );
            
        $slice->afterEachOther();
            
        $i = 1;
        foreach ($slice->codeRegex('/^a[\d]+$/')->afterMilestone() as $milestone) {
            $milestone->criteria->when(
                '[aspects/readiness] > %0.2f', 
                0.3 * $i++ / $total
            );
        }
            
        if (!$slice->sum()->isZero()) {
            foreach ($slice->onlyActivities() as $activity) {
                $activity->criteria->when(
                    '[artifacts/requirements/actors/compliance] > %0.2f', 
                    $slice->sumUntil($activity)->div($slice->sum())
                );
            }
        }
    }
        
}

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

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/Abstract.php';

/**
 * Activity cost is estimated by performer
 * 
 * @package Activity_Plugin
 */
class Activity_Plugin_IsCostEstimated extends Activity_Plugin_Abstract
{

    /**
     * Execute it
     *
     * @return boolean
     */
    public function execute()
    {
        // milestones can't become alive in tracker
        if ($this->_activity->isMilestone())
            return false;

        if (!$this->_activity->isIssueExist())
            return false;
            
        return $this->_issue->isCostEstimated();
    }
                            
}

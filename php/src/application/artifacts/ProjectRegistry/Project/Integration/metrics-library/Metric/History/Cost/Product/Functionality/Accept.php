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
 * @version $Id: Total.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 *
 */

/**
 * Cost of one functional requirement to accept by end-user, architect, etc.
 * 
 * @package Artifacts
 * @see Metric_Artifacts_Product_Functionality_Accepted
 */
class Metric_History_Cost_Product_Functionality_Accept extends Metric_Abstract
{

    /**
     * Load this metric
     *
     * @return void
     * @see theMetrics::_attachMetric()
     */
    public function reload()
    {
        // in USD
        $this->value = 10;
    }
            
}

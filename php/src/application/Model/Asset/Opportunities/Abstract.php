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
 * @version $Id: Abstract.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 *
 */

/**
 * Interface to the database of Opportunities
 *
 * @package Model
 */
abstract class Model_Asset_Opportunities_Abstract extends Model_Asset_Abstract
{
    
    /**
     * Get full list of Opportunities (IDs)
     *
     * @return string[]
     **/
    abstract public function retrieveAll();
    
    /**
     * Get full details of an Opportunity by email
     *
     * @param string Id of the Opportunity
     * @param theOpportunity Object to fill with data
     * @return mixed
     **/
    abstract public function deriveById($id, theOpportunity $opportunity);
    
}
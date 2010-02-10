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
 * @version $Id: Vision.php 651 2010-02-10 17:50:05Z yegor256@yahoo.com $
 *
 */

/**
 * Escape TeX symbols
 *
 * @package Artifacts
 */
class Sheet_Helper_Tex
{

    /**
     * Escape TeX chars
     *
     * @return string
     */
    public function tex($str) 
    {
        return addcslashes($str, '[]{}^%&\\$_#');
    }

}

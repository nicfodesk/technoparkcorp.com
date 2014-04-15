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
 * Static controller (static pages)
 *
 * @package Controllers
 */
class StaticController extends FaZend_Controller_Action
{

    /**
     * Default and the only action for this controller
     *
     * @return void
     */
    public function indexAction() 
    {
        try {
            // try to create an article with the give PAGE address
            $article = $this->view->article = Model_Article::createByLabel($this->_getParam('page'));
        } catch (Model_Article_NotFound $e) {
            assert($e instanceof Exception); // for ZCA only
            return $this->_redirect('system/404');
        }    

        // change View script, if this is a home page (/home)
        if ($article->page === 'home') {
            $this->_helper->viewRenderer('home');
            return;
        }    

        $container = $this->view->navigation();
        Model_Navigation::populateNavigation($container, $article->page);

        // change content if the PHTML script found    
        $scripts = array(
            dirname(APPLICATION_PATH . '/views/scripts/content/' . $article->page) => '_any.phtml',
            APPLICATION_PATH . '/views/scripts/content' => $article->page . '.phtml',
        );

        // try to render all scripts, one-by-one
        foreach ($scripts as $path=>$script) {
            if (!file_exists($path . '/' . $script)) {
                continue;
            }
            $this->view->addScriptPath($path);
            $article->text = $this->view->render($script);
        }

        // make this menu element visible, no matter what
        $page = $this->view->navigation()->findOneBy('path', $article->page);
        if ($page) {
            $page->visible = true;
            $page->parent->visible = true;
        }

        if ($this->getResponse()->getBody()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
        }
        // parse special XML meta symbols, like ${url:about/news}
        //$this->view->content = XMLDocument::parseText($this->view->content);
    }

    /**
     * Download document in PDF
     *
     * @return void
     */
    public function pdfAction() 
    {
        $article = Model_Article::createByLabel($this->_getParam('page'));
        return $this->_returnPDF($article->asPdf());
    }

}
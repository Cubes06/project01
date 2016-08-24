<?php

    class PortfolioController extends Zend_Controller_Action {

        public function init() {
            /* Initialize action controller here */
        }

        public function indexAction() {
            
            $request = $this->getRequest();
            
            $sitemapPageId = (int) $request->getParam('sitemap_page_id');
            
            if ($sitemapPageId <= 0) {
                throw new Zend_Controller_Router_Exception('Invalid sitemap page id: ' . $sitemapPageId, 404);
            }
            
            $cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();
            $sitemapPage = $cmsSitemapPageDbTable->getSitemapPageById($sitemapPageId);
            
            if (!$sitemapPage) {
                throw new Zend_Controller_Router_Exception('No sitemap page is found for id: ' . $sitemapPageId, 404);
            }
            
            $this->view->sitemapPage = $sitemapPage;
            
            if ( //check if user is not logged in then preview is not available for disabled pages
                    ($sitemapPage['status'] == Application_Model_DbTable_CmsSitemapPages::STATUS_DISABLED)
                    && !Zend_Auth::getInstance()->hasIdentity()
            ) {
                throw new Zend_Controller_Router_Exception('Sitemap page is disabled');
            }
            /////////////////////////////////////////////////////
            
            $cmsPortfoliosDbTable = new Application_Model_DbTable_CmsPortfolios();
            // $select jed objekat klase Zend Db
            $select = $cmsPortfoliosDbTable->select();
            $select->where('status = ?', Application_Model_DbTable_CmsPortfolios::STATUS_ENABLED)
                    ->order('order_number ASC');
            //debug za db select - vrace se sql upit
            //die($select->assemble());
                   
            $portfolios = $cmsPortfoliosDbTable->fetchAll($select);
            
            
            $cmsPorfolioCategoriesDbTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            $portfoliosCategories = $cmsPorfolioCategoriesDbTable->search(array(
                'filters' => array(
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $this->view->portfolios = $portfolios;
            $this->view->portfoliosCategories = $portfoliosCategories;
         
        }

        public function productAction() {
            
            $request = $this->getRequest();
            $id = (int) $request->getParam("id");
            
            //filtriranje
            $id = trim($id);
            $id = (int) $id;
            
            //validacija
            if (empty($id)) {
                throw new Zend_Controller_Router_Exception("No portfolio id", 404);
            }
            
            
            
            $cmsPortfoliosDbTable = new Application_Model_DbTable_CmsPortfolios();
            $select = $cmsPortfoliosDbTable->select();
            $select->where("id = ?", $id)
                    ->where("status = ?", Application_Model_DbTable_CmsPortfolios::STATUS_ENABLED);
            
            $foundPortfolios = $cmsPortfoliosDbTable->fetchAll($select);
            if (count($foundPortfolios) <= 0) {
                throw new Zend_Controller_Router_Exception("No portfolio is found for id: " . $id, 404);
            }
            
            $portfolio = $foundPortfolios[0];
            //isto kao gore   $portfolio = array_shift($foundPortfolios);
            $portfolioSlug = $request->getParam('portfolio_slug');
            
            if (empty($portfolioSlug)) {
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                           ->gotoRoute(
                                    array(
                                        'id' => $portfolio['id'],
                                        'portfolio_slug' => $portfolio['title']
                                    ), 
                                   'portfolio-route', 
                                   true
                            );
            }
            
            //Fetching all other portfolios
            $select = $cmsPortfoliosDbTable->select();
            $select->where('status = ?', Application_Model_DbTable_CmsPortfolios::STATUS_ENABLED)
                    ->where('id != ?', $id)
                    ->order('order_number ASC');
            
            $portfolios = $cmsPortfoliosDbTable->fetchAll($select);
            
            $this->view->portfolios = $portfolios;
            $this->view->portfolio = $portfolio;
            
        }

    }

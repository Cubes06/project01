<?php

    class IndexController extends Zend_Controller_Action {

        public function init() {
            /* Initialize action controller here */
        }

        public function indexAction() {

            
            $cmsIndexSlidesDbTable = new Application_Model_DbTable_CmsIndexSlides();

            $indexSlides = $cmsIndexSlidesDbTable->search(array(
                'filters' => array(
                    'status' => Application_Model_DbTable_CmsIndexSlides::STATUS_ENABLED
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));

            $this->view->indexSlides = $indexSlides;
            
            
            $cmsPortfoliosDbTable = new Application_Model_DbTable_CmsPortfolios();
            $portfolios = $cmsPortfoliosDbTable->search(array(
                'filters' => array(
                    'status' => Application_Model_DbTable_CmsPortfolios::STATUS_ENABLED
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            
            $cmsPorfolioCategoriesDbTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            $portfoliosCategories = $cmsPorfolioCategoriesDbTable->search(array(
                'filters' => array(
                    'status' => Application_Model_DbTable_CmsPortfoliosCategories::STATUS_ENABLED
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
//			?><pre><?php
//			print_r($portfoliosCategories); die();
//			foreach ($portfoliosCategories as $key => $value) {
//				$portfoliosCategories[$value]['name'] = Application_Model_Misc::adjustURL($portfoliosCategories[$value]['name']);
//				print_r($portfoliosCategories[$value]['name']);
//			}
//			die();
            $this->view->portfolios = $portfolios;
            $this->view->portfoliosCategories = $portfoliosCategories;
            
            
        }

    }

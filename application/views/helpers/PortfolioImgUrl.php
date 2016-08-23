<?php

    class Zend_View_Helper_PortfolioImgUrl extends Zend_View_Helper_Abstract {
        public function portfolioImgUrl($portfolio) {
            
            $portfolioImgFileName = $portfolio['id'] . '.jpg';
            
            $portfolioImgFilePath = PUBLIC_PATH . "/uploads/portfolios/" . $portfolioImgFileName;
            
            //Helper ima propery view koji je Zend_View
            //i preko kojeg pozivamo ostale view helpere
            //na primer $this->view->baseUrl()
            
            
            if (is_file($portfolioImgFilePath)) {
                return $this->view->baseUrl('/uploads/portfolios/' . $portfolioImgFileName);
            }
            else {
                return "";
            }
            
        }

    }


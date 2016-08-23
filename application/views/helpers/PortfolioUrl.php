<?php

    class Zend_View_Helper_PortfolioUrl extends Zend_View_Helper_Abstract {
        
        public function portfolioUrl($portfolio) {
            
            return $this->view->url(
                   array(
                       'id' => $portfolio['id'],
                       'portfolio_slug' => $portfolio['title']
                   ), 
                   'portfolio-route', 
                   true
            );
            
        }//endf
        

    }//end of class: Zend_View_Helper_PortfolioUrl


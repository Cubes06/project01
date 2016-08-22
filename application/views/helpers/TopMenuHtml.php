<?php

    class Zend_View_Helper_TopMenuHtml extends Zend_View_Helper_Abstract {
        
        public function topMenuHtml() {       
            
            $cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();
            
            $topMenuSitemapPages = $cmsSitemapPageDbTable->search(array(
                'filters' => array(
                    'parent_id' => 0,
                    'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $this->view->placeholder('topMenuHtml')->exchangeArray(array());
            
            $this->view->placeholder('topMenuHtml')->captureStart();
            ?>

            <ul class="nav navbar-nav mainMenu" id="main-menu">
            
                <li>
                    <a href="<?php echo $this->view->baseUrl('/'); ?>">NASLOVNA</a>
                </li>

                <?php foreach ($topMenuSitemapPages as $sitemapPage) {
                        $secondLevelSitemapPages = $cmsSitemapPageDbTable->search(array(
                            'filters' => array(
                                'parent_id' => $sitemapPage['id'],
                                'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED
                            ),
                            'orders' => array(
                                'order_number' => 'ASC'
                            )
                        ));
                        ?>

                        <li <?php echo (!empty($secondLevelSitemapPages)) ? ' class="dropdown" ' : '' ?> >

                            <a href="<?php echo $this->view->sitemapPageUrl($sitemapPage['id']); ?>" <?php echo (!empty($secondLevelSitemapPages)) ? ' class="dropdown-toggle" data-toggle="dropdown" ' : '' ?> > <?php echo $this->view->escape($sitemapPage['short_title']); ?> </a>

                            <?php if (!empty($secondLevelSitemapPages)) { ?>
                                <ul class="dropdown-menu" aria-labelledby="menu-item-<?php echo $sitemapPage['id']; ?>" role="menu">
                                    <?php foreach ($secondLevelSitemapPages as $secondLevelSitemapPage) { ?>
                                        <li role="menuitem"><a href="<?php echo $this->view->sitemapPageUrl($secondLevelSitemapPage['id']); ?>" tabindex="-1"><?php echo $this->view->escape($secondLevelSitemapPage['short_title']); ?></a></li>
                                    <?php } ?>
                                </ul>

                            <?php } ?>
                        </li>
                <?php } ?>

                <li>
                    <a href="<?php echo $this->view->baseUrl('/admin_session'); ?>"><i class="fa fa-user"></i> Login (Temp)</a>
                </li>
            </ul>

            <?php
            
            $this->view->placeholder('topMenuHtml')->captureEnd();
            
            return $this->view->placeholder('topMenuHtml')->toString();
            
        } //end of function
        
    } //end of class: Zend_View_Helper_TopMenuHtml
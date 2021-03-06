<?php

    class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

        protected function _initRouter() {
            //ensure that database is configured
            $this->bootstrap('db');

            $sitemapPageTypes = array(
                'StaticPage' => array(
                    'title' => 'Static Page',
                    'subtypes' => array(
                        // 0 means unlimited number
                        'StaticPage' => 0
                    )
                ),
                'PhotoGalleriesPage' => array(
                    'title' => 'Photo Galleries Page',
                    'subtypes' => array(
                        
                    )
                ),
                'ContactPage' => array(
                    'title' => 'Contact Page',
                    'subtypes' => array(
                        
                    )
                ),
                'PortfolioPage' => array(
                    'title' => 'Portfolio Page',
                    'subtypes' => array(
                        
                    )
                ),
            );


            $rootSitemapPageTypes = array(
                'StaticPage' => 0,
                'PhotoGalleriesPage' => 1,
                'ContactPage' => 1,
                'PortfolioPage' => 1
            );


            Zend_Registry::set('sitemapPageTypes', $sitemapPageTypes);
            Zend_Registry::set('rootSitemapPageTypes', $rootSitemapPageTypes);

            $router = Zend_Controller_Front::getInstance()->getRouter();

            $router instanceof Zend_Controller_Router_Rewrite;

            $sitemapPagesMap = Application_Model_DbTable_CmsSitemapPages::getSitemapPagesMap();

            
            foreach ($sitemapPagesMap as $sitemapPageId => $sitemapPageMap) {

                if ($sitemapPageMap['type'] == 'StaticPage') {
                    $router->addRoute('static-page-route-' . $sitemapPageId, 
                            new Zend_Controller_Router_Route_Static(
                                $sitemapPageMap['url'], 
                                array(
                                    'controller' => 'staticpage',
                                    'action' => 'index',
                                    'sitemap_page_id' => $sitemapPageId
                                )
                            )
                    );
                }


                if ($sitemapPageMap['type'] == 'PhotoGalleriesPage') {
                    $router->addRoute('static-page-route-' . $sitemapPageId, 
                            new Zend_Controller_Router_Route_Static(
                                $sitemapPageMap['url'], 
                                array(
                                    'controller' => 'photogalleries',
                                    'action' => 'index',
                                    'sitemap_page_id' => $sitemapPageId
                                )
                            )
                    );

                    $router->addRoute('photo-gallery-route', 
                            new Zend_Controller_Router_Route(
                            $sitemapPageMap['url'] . '/:id/:photo_gallery_slug', 
                            array(
                                'controller' => 'photogalleries',
                                'action' => 'gallery',
                                'sitemap_page_id' => $sitemapPageId
                            )
                        )
                    );
                }
                
                if ($sitemapPageMap['type'] == 'ContactPage') {
                    $router->addRoute( 
                        'static-page-route-' . $sitemapPageId, 
                        new Zend_Controller_Router_Route_Static (
                                $sitemapPageMap['url'],
                                array(
                                    'controller' => 'contact',
                                    'action' => 'index',
                                    'sitemap_page_id' => $sitemapPageId
                                )
                        )
                    );
                }
                
                if ($sitemapPageMap['type'] == 'PortfolioPage') {
                    $router->addRoute( 
                        'static-page-route-' . $sitemapPageId, 
                        new Zend_Controller_Router_Route_Static (
                                $sitemapPageMap['url'],
                                array(
                                    'controller' => 'portfolio',
                                    'action' => 'index',
                                    'sitemap_page_id' => $sitemapPageId
                                )
                        )
                    );
                    
                    $router->addRoute(
                        'portfolio-route', 
                        new Zend_Controller_Router_Route (
                                $sitemapPageMap['url'] . '/product/:id/:portfolio_slug',
                                array(
                                    'controller' => 'portfolio',
                                    'action' => 'product',
                                    'portfolio_slug' => ''
                                )
                        )
                    ); 
                                
                }
                
                
            }//foreach
            
        }//endf _initRouter

    } // end of class: Bootstrap

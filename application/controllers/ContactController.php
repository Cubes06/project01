<?php

    class ContactController extends Zend_Controller_Action {

        public function init() {
            /* Initialize action controller here */
        }

        public function indexAction() {
            $request = $this->getRequest();
            
            $sitemapPageId = (int) $request->getParam('sitemap_page_id');
            
            if ($sitemapPageId <= 0) {
                //$sitemapPageId = 34;
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
        }//endf
        
        
        
        public function askmemberAction() {
            
            $request = $this->getRequest();
            
            $id = (int) $request->getParam('id'); //(int) pretvara slova u nule
            
            if ($id <= 0) {
                throw new Zend_Controller_Router_Exception('Invalid member id: ' . $id, 404); // ovako prekidamo izvrsavanje programa i prikazujemo 'page not found'
            }
            
            $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
            $member = $cmsMembersTable->getMemberById($id);
            
            if (empty($member)) {
                throw new Zend_Controller_Router_Exception('No member is found with id: ' . $id, 404);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger');  

            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors'),
            );
            
            
            $this->view->member = $member;
        }

    }

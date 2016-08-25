<?php
    
    use Intervention\Image\ImageManagerStatic as Image;

    class Admin_PortfolioscategoriesController extends Zend_Controller_Action {
        
        
        public function indexAction() {
            
            $flashMessenger = $this->getHelper('FlashMessenger');
            
            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors')
            );
            
            
            $cmsPortfoliocategoriesDbTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            $portfolioCategories = $cmsPortfoliocategoriesDbTable->search(array(
                'filters' => array(
                    //'status' => Application_Model_DbTable_CmsPortfoliosCategories::STATUS_DISABLED
                    //'first_name_search' => 'Ale'
                    //'first_name' => array('Aleksandra', 'Bojan')
                    //'id' => array(1, 3, 5, 6)
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $this->view->portfolioCategories = $portfolioCategories;
            $this->view->systemMessages = $systemMessages;
        }
        
        
        public function addAction() {
            
            $request = $this->getRequest(); //podaci iz url-a iz forme sa koje dolazimo 
            $flashMessenger = $this->getHelper('FlashMessenger');  // za prenosenje sistemskih poruka

            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors'),
            );

            $form = new Application_Form_Admin_PortfolioCategoryAdd();
            
            //default form data
            $form->populate(array(
                
            ));
            
            // kad prvi put dolazimo onda je get method, a ako smo preko forme onda je post method
            if ($request->isPost() && $request->getPost('task') === 'save') {
                try {
                    //check form is valid
                    if (!$form->isValid($request->getPost())) {
                        throw new Application_Model_Exception_InvalidInput('Invalid data was sent for new portfolioCategory');
                    }
                    
                    //get form data
                    $formData = $form->getValues();

                    
                    $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();

                    //insert portfolioCategory returns ID of the new portfolioCategory
                    $portfolioCategoryId = $cmsPortfoliocategoriesTable->insertPortfoliocategory($formData);


                    $flashMessenger->addMessage('Portfolio category has been saved', 'success');
                    
                    //redirect to same or another page
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolioscategories',
                                'action' => 'index'
                                    ), 'default', true);
                } 
                catch (Application_Model_Exception_InvalidInput $ex) {
                    $systemMessages['errors'][] = $ex->getMessage();
                }
            }

            $this->view->systemMessages = $systemMessages;
            $this->view->form = $form;
            
        }//endf
        
        
        public function editAction() {
		
	    $request = $this->getRequest();
            
            $id = (int) $request->getParam('id'); //(int) pretvara slova u nule
            
            if ($id <= 0) {
                throw new Zend_Controller_Router_Exception('Invalid portfolioCategory id: ' . $id, 404); // ovako prekidamo izvrsavanje programa i prikazujemo 'page not found'
            }
            
            $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            $portfolioCategory = $cmsPortfoliocategoriesTable->getPortfoliocategoryById($id);
            
            if (empty($portfolioCategory)) {
                throw new Zend_Controller_Router_Exception('No portfolioCategory is found with id: ' . $id, 404);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger');  

            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors'),
            );

            $form = new Application_Form_Admin_PortfolioCategoryEdit();
            
            // kad prvi put dolazimo onda je get method, a ako smo preko forme onda je post method
            if ($request->isPost() && $request->getPost('task') === 'update') {
                //default form data
                
                try {
                        //check form is valid
                        if (!$form->isValid($request->getPost())) {
                                throw new Application_Model_Exception_InvalidInput('Invalid data was sent for portfolioCategory');
                        }
                        
                        //get form data
                        $formData = $form->getValues();

                        
                        //Radimo update postojeceg zapisa u tabeli
                        $cmsPortfoliocategoriesTable->updatePortfoliocategory($portfolioCategory['id'], $formData);

                        //set system message
                        $flashMessenger->addMessage('Portfoliocategory has been updated', 'success');
                        
                        //redirect to same or another page
                        $redirector = $this->getHelper('Redirector');
                        $redirector->setExit(true)
                                   ->gotoRoute(
                                            array(
                                                'controller' => 'admin_portfolioscategories',
                                                'action' => 'index'
                                            ), 
                                           'default', 
                                           true
                        );
                }
                catch (Application_Model_Exception_InvalidInput $ex) {
                        $systemMessages['errors'][] = $ex->getMessage();
                }
            }
            else {
                //default form data
                $form->populate($portfolioCategory);
            }

            $this->view->systemMessages = $systemMessages;
            $this->view->form = $form;
            
            $this->view->portfolioCategory = $portfolioCategory;
            
	}
        
     
        public function deleteAction() {
            
            $request = $this->getRequest();
            
            if (!$request->isPost() || $request->getPost('task') != 'delete') {
                // request is not post or task is not delete
                // redirect to index page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolioCategory id: ' . $id);
                }

                $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
                $portfolioCategory = $cmsPortfoliocategoriesTable->getPortfoliocategoryById($id);

                if (empty($portfolioCategory)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolioCategory is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliocategoriesTable->deletePortfoliocategory($id);
                $flashMessenger->addMessage('Portfoliocategory ' . $portfolioCategory['name'] . ' has been deleted.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolioscategories',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            
        }
        
        
        public function disableAction() {
            
            $request = $this->getRequest();
            
            if (!$request->isPost() || $request->getPost('task') != 'disable') {
                // request is not post or task is not disable
                // redirect to index page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolioCategory id: ' . $id);
                }

                $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
                $portfolioCategory = $cmsPortfoliocategoriesTable->getPortfoliocategoryById($id);

                if (empty($portfolioCategory)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolioCategory is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliocategoriesTable->disablePortfoliocategory($id);
                $flashMessenger->addMessage('Portfoliocategory ' . $portfolioCategory['name'] . ' has been disabled.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolioscategories',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            

            
        }
        
              
        public function enableAction() {
            
            $request = $this->getRequest();
            
            if (!$request->isPost() || $request->getPost('task') != 'enable') {
                // request is not post or task is not disable
                // redirect to index page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolioCategory id: ' . $id);
                }

                $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
                $portfolioCategory = $cmsPortfoliocategoriesTable->getPortfoliocategoryById($id);

                if (empty($portfolioCategory)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolioCategory is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliocategoriesTable->enablePortfoliocategory($id);
                $flashMessenger->addMessage('Portfoliocategory ' . $portfolioCategory['name'] . ' has been enabled.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolioscategories',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            

            
        }
        
        
        public function updateorderAction() {
            
            $request = $this->getRequest();
            
            if (!$request->isPost() || $request->getPost('task') != 'saveOrder') {
                // request is not post or task is not disable
                // redirect to index page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            
            try {
                
                $sortedIds = $request->getPost('sorted_ids');
                
                if (empty($sortedIds)) {
                    throw new Application_Model_Exception_InvalidInput('Sorted ids are not sent.');
                }
                $sortedIds = trim($sortedIds, ' ,');
                if (!preg_match('/^[0-9]+(,[0-9]+)*$/', $sortedIds)) {
                    throw new Application_Model_Exception_InvalidInput('Invalid sorted ids: ' . $sortedIds);
                }
                
                $sortedIds = explode(',', $sortedIds);
                
                
                $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
                
                $cmsPortfoliocategoriesTable->updateOrderOfPortfoliocategories($sortedIds);
                
                
                $flashMessenger->addMessage('Order is successfully saved', 'success');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);
            }
               
            $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolioscategories',
                            'action' => 'index'
                            ), 'default', true);          
        }
        
        
        public function dashboardAction() {
            
            $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            
//            $active = $cmsPortfoliocategoriesTable->getActivePortfoliocategories();
//            $total = $cmsPortfoliocategoriesTable->getTotalPortfoliocategories();
            
            $portfolioCategoriesActive = $cmsPortfoliocategoriesTable->count(array(
               'status' => Application_Model_DbTable_CmsPortfoliosCategories::STATUS_ENABLED
            ));
            $portfolioCategoriesTotal = $cmsPortfoliocategoriesTable->count();
            
            $this->view->active =  $portfolioCategoriesActive;
            $this->view->total =  $portfolioCategoriesTotal;
            
        }
        
        
        public function dashboard2Action() {
            
            $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            
            $active = $cmsPortfoliocategoriesTable->getActivePortfoliocategories();
            $total = $cmsPortfoliocategoriesTable->getTotalPortfoliocategories();
            
            $this->view->active =  $active;
            $this->view->total =  $total;
            
        }
        
        
        public function dashboard3Action() {
            
            Zend_Layout::getMvcInstance()->disableLayout();
            
            //$this->getHelper("viewRenderer")->setNoRender(true);
            $this->_helper->viewRenderer->setNoRender(true);
            
            $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            
            $active = $cmsPortfoliocategoriesTable->getActivePortfoliocategories();
            $total = $cmsPortfoliocategoriesTable->getTotalPortfoliocategories();
            
            
            echo $active . " / " . $total;
            
        }
        
        public function getstatsAction() {
            $cmsPortfoliocategoriesTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            
            $active = $cmsPortfoliocategoriesTable->getActivePortfoliocategories();
            $total = $cmsPortfoliocategoriesTable->getTotalPortfoliocategories();
            
            $responseJson = new Application_Model_JsonResponse();
            
            $responseJson->setPayload(array(
                'active' => $active,
                'total' => $total
            ));
            
            $this->getHelper('Json')->sendJson($responseJson);
        }
        
    }


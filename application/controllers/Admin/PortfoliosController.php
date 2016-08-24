<?php
    
    use Intervention\Image\ImageManagerStatic as Image;

    class Admin_PortfoliosController extends Zend_Controller_Action {
        

        
        public function indexAction() {
            
            $flashMessenger = $this->getHelper('FlashMessenger');
            
            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors')
            );
            
            // prikaz svih portfolio-a
            $cmsPortfoliosDbTable = new Application_Model_DbTable_CmsPortfolios();
            
            
            
            // $select je od objekat klase Zend_Db_Select
//            $select = $cmsPortfoliosDbTable->select();
//            $select->order('order_number ASC');
            
            
            //debug za db select - vrace se sql upit
            //die($select->assemble());
                   
//            $portfolios = $cmsPortfoliosDbTable->fetchAll($select);
            
            $portfolios = $cmsPortfoliosDbTable->search(array(
                'filters' => array(
                    
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            
            $cmsPorfolioCategoriesDbTable = new Application_Model_DbTable_CmsPortfoliosCategories();

            
            $formated_portfolios = array();
            
            foreach ($portfolios as $key => $value) {
                $cats = array();
                if (isset($value['data_categories']) && !is_array($value['data_categories'])) {
                    $cats = explode(', ', $value['data_categories']);
                }
                
                $value['data_categories'] = "";
                foreach ($cats as $key => $catValue) { 
                    $c = $cmsPorfolioCategoriesDbTable->getPortfolioCategoryNameById($catValue);
    
                    $value['data_categories'] .= $c . ', ';
                    
                }
                //$value['data_categories'] = substr($value['data_categories'], 0, -2);
                $value['data_categories'] = rtrim($value['data_categories'], ', ');
                $line = $value;
                $formated_portfolios[] = $line;
            }
            
            $this->view->portfolios = $formated_portfolios;
            $this->view->systemMessages = $systemMessages;
        }
        
        
        public function addAction() {
            
            $request = $this->getRequest(); //podaci iz url-a iz forme sa koje dolazimo 
            $flashMessenger = $this->getHelper('FlashMessenger');  // za prenosenje sistemskih poruka

            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors'),
            );

            $form = new Application_Form_Admin_PortfolioAdd();
            
            //default form data
            $form->populate(array(
                
            ));
            
            // kad prvi put dolazimo onda je get method, a ako smo preko forme onda je post method
            if ($request->isPost() && $request->getPost('task') === 'save') {
                try {
                    //check form is valid
                    if (!$form->isValid($request->getPost())) {
                        throw new Application_Model_Exception_InvalidInput('Invalid data was sent for new portfolio');
                    }
                    
                    //get form data
                    $formData = $form->getValues();

                    //remove key portfolio_photo from form data because there is no column 'portfolio_photo' in cms_portfolios table
                    unset($formData['portfolio_photo']);
                    
                    $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();

                    //insert portfolio returns ID of the new portfolio
                    $portfolioId = $cmsPortfoliosTable->insertPortfolio($formData);

                    if ($form->getElement('portfolio_photo')->isUploaded()) {
                        //photo is uploaded

                        $fileInfos = $form->getElement('portfolio_photo')->getFileInfo('portfolio_photo');
                        $fileInfo = $fileInfos['portfolio_photo'];


                        try {
                            //open uploaded photo in temporary directory
                            $portfolioPhoto = Image::make($fileInfo['tmp_name']);

                            $portfolioPhoto->fit(370, 247);

                            $portfolioPhoto->save(PUBLIC_PATH . '/uploads/portfolios/' . $portfolioId . '.jpg');
                        }
                        catch (Exception $ex) {

                            $flashMessenger->addMessage('Portfolio has been saved but error occured during image processing', 'errors');
                            //redirect to same or another page
                            $redirector = $this->getHelper('Redirector');
                            $redirector->setExit(true)
                                    ->gotoRoute(array(
                                        'controller' => 'admin_portfolios',
                                        'action' => 'edit',
                                        'id' => $portfolioId
                                            ), 'default', true);
                        }
                        //$fileInfo = $_FILES['portfolio_photo'];
                    }

                    $flashMessenger->addMessage('Portfolio has been saved', 'success');
                    
                    //redirect to same or another page
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolios',
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
                throw new Zend_Controller_Router_Exception('Invalid portfolio id: ' . $id, 404); // ovako prekidamo izvrsavanje programa i prikazujemo 'page not found'
            }
            
            $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
            $portfolio = $cmsPortfoliosTable->getPortfolioById($id);
            
            if (empty($portfolio)) {
                throw new Zend_Controller_Router_Exception('No portfolio is found with id: ' . $id, 404);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger');  

            $systemMessages = array(
                'success' => $flashMessenger->getMessages('success'),
                'errors' => $flashMessenger->getMessages('errors'),
            );

            $form = new Application_Form_Admin_PortfolioEdit();
            
            // kad prvi put dolazimo onda je get method, a ako smo preko forme onda je post method
            if ($request->isPost() && $request->getPost('task') === 'update') {
                //default form data
                
                try {
                        //check form is valid
                        if (!$form->isValid($request->getPost())) {
                                throw new Application_Model_Exception_InvalidInput('Invalid data was sent for portfolio');
                        }
                        
                        //get form data
                        $formData = $form->getValues();

                        unset($formData['portfolio_photo']);

                        if ($form->getElement('portfolio_photo')->isUploaded()) {
                            //photo is uploaded

                            $fileInfos = $form->getElement('portfolio_photo')->getFileInfo('portfolio_photo');
                            $fileInfo = $fileInfos['portfolio_photo'];

                            try {
                                //open uploaded photo in temporary directory
                                $portfolioPhoto = Image::make($fileInfo['tmp_name']);

                                $portfolioPhoto->fit(370, 247);

                                $portfolioPhoto->save(PUBLIC_PATH . '/uploads/portfolios/' . $portfolio['id'] . '.jpg');

                            } 
                            catch (Exception $ex) {

                                    throw new Application_Model_Exception_InvalidInput('Error occured during image processing');

                            }
                            //$fileInfo = $_FILES['portfolio_photo'];
                        }
                        
                        //Radimo update postojeceg zapisa u tabeli
                        $cmsPortfoliosTable->updatePortfolio($portfolio['id'], $formData);

                        //set system message
                        $flashMessenger->addMessage('Portfolio has been updated', 'success');
                        
                        //redirect to same or another page
                        $redirector = $this->getHelper('Redirector');
                        $redirector->setExit(true)
                                   ->gotoRoute(
                                            array(
                                                'controller' => 'admin_portfolios',
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
                $form->populate($portfolio);
            }

            $this->view->systemMessages = $systemMessages;
            $this->view->form = $form;
            
            $this->view->portfolio = $portfolio;
            
	}
        
     
        public function deleteAction() {
            
            $request = $this->getRequest();
            
            if (!$request->isPost() || $request->getPost('task') != 'delete') {
                // request is not post or task is not delete
                // redirect to index page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolio id: ' . $id);
                }

                $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
                $portfolio = $cmsPortfoliosTable->getPortfolioById($id);

                if (empty($portfolio)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolio is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliosTable->deletePortfolio($id);
                $flashMessenger->addMessage('Portfolio ' . $portfolio['first_name'] . ' ' . $portfolio['last_name'] . ' has been deleted.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolios',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
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
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolio id: ' . $id);
                }

                $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
                $portfolio = $cmsPortfoliosTable->getPortfolioById($id);

                if (empty($portfolio)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolio is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliosTable->disablePortfolio($id);
                $flashMessenger->addMessage('Portfolio ' . $portfolio['first_name'] . ' ' . $portfolio['last_name'] . ' has been disabled.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolios',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
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
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);
            }
            
            $flashMessenger = $this->getHelper('FlashMessenger'); 
            
            try {
                $id = (int) $request->getPost('id'); // isto sto i read $_POST['id']

                if ($id <= 0) {
                    throw new Application_Model_Exception_InvalidInput('Invalid portfolio id: ' . $id);
                }

                $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
                $portfolio = $cmsPortfoliosTable->getPortfolioById($id);

                if (empty($portfolio)) {
                    throw new Application_Model_Exception_InvalidInput('No portfolio is found with id: ' . $id, 'errors');
                }

                $cmsPortfoliosTable->enablePortfolio($id);
                $flashMessenger->addMessage('Portfolio ' . $portfolio['first_name'] . ' ' . $portfolio['last_name'] . ' has been enabled.', 'success');
                    $redirector = $this->getHelper('Redirector');
                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_portfolios',
                                'action' => 'index'
                                ), 'default', true);
            } 
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
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
                            'controller' => 'admin_portfolios',
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
                
                
                $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
                
                $cmsPortfoliosTable->updateOrderOfPortfolios($sortedIds);
                
                
                $flashMessenger->addMessage('Order is successfully saved', 'success');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);
            }
            catch (Application_Model_Exception_InvalidInput $ex) {
                $flashMessenger->addMessage($ex->getMessage(), 'errors');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);
            }
               
            $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_portfolios',
                            'action' => 'index'
                            ), 'default', true);          
        }
        
        
        public function dashboardAction() {
            
            $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
            
//            $active = $cmsPortfoliosTable->getActivePortfolios();
//            $total = $cmsPortfoliosTable->getTotalPortfolios();
            
            $portfoliosActive = $cmsPortfoliosTable->count(array(
               'status' => Application_Model_DbTable_CmsPortfolios::STATUS_ENABLED
            ));
            $portfoliosTotal = $cmsPortfoliosTable->count();
            
            $this->view->active =  $portfoliosActive;
            $this->view->total =  $portfoliosTotal;
            
        }
        
        
        public function dashboard2Action() {
            
            $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
            
            $active = $cmsPortfoliosTable->getActivePortfolios();
            $total = $cmsPortfoliosTable->getTotalPortfolios();
            
            $this->view->active =  $active;
            $this->view->total =  $total;
            
        }
        
        
        public function dashboard3Action() {
            
            Zend_Layout::getMvcInstance()->disableLayout();
            
            //$this->getHelper("viewRenderer")->setNoRender(true);
            $this->_helper->viewRenderer->setNoRender(true);
            
            $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
            
            $active = $cmsPortfoliosTable->getActivePortfolios();
            $total = $cmsPortfoliosTable->getTotalPortfolios();
            
            
            echo $active . " / " . $total;
            
        }
        
        public function getstatsAction() {
            $cmsPortfoliosTable = new Application_Model_DbTable_CmsPortfolios();
            
            $active = $cmsPortfoliosTable->getActivePortfolios();
            $total = $cmsPortfoliosTable->getTotalPortfolios();
            
            $responseJson = new Application_Model_JsonResponse();
            
            $responseJson->setPayload(array(
                'active' => $active,
                'total' => $total
            ));
            
            $this->getHelper('Json')->sendJson($responseJson);
        }
        
    }


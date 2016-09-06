<?php

    class IndexController extends Zend_Controller_Action {

        public function init() {
            /* Initialize action controller here */
        }

        public function indexAction() {
//			$sql0 = 'DROP TABLE IF EXISTS big;';
//
//			$col_number = 685;
//			$sql1 = 'CREATE TABLE big (';
//			for ($i=1; $i<$col_number; $i++) {
//				$sql1 .= 'col' . $i . ' INT, ';
//			}
//			$sql1 = rtrim($sql1, ', ');
//			$sql1 .=  ');';
//
//
//			$sql2 = 'INSERT INTO `big`(';
//			for ($i=1; $i<$col_number; $i++) {
//				$sql2 .= 'col' . $i . ', ';
//			}
//			$sql2 = rtrim($sql2, ', ');
//			$sql2 .= ') VALUES (';
//			for ($i=1; $i<$col_number; $i++) {
//				$sql2 .= $i . ', ';
//			}
//			$sql2 = rtrim($sql2, ', ');
//			$sql2 .=  ');';
//
//			$sql3 = 'ALTER TABLE `big` ADD PRIMARY KEY(`col1`);';
//
//			print_r($sql0); print_r($sql1); print_r($sql2); print_r($sql3); die();
//			
//			
//            $cmsUsersDbTable = new Application_Model_DbTable_CmsUsers();
//            $data = $cmsUsersDbTable->search(array());
//            $cmsPortfoliosDbTable = new Application_Model_DbTable_CmsPortfolios();
//            $data = $cmsPortfoliosDbTable->search(array());
			
//			$cmsBigDbTable = new Application_Model_DbTable_Big();
//			$data = $cmsBigDbTable->search(array());
//			//print_r($data); die();
//			
//            if (empty($data)) {		
//                die();
//            }
//            $columnNames = array_keys($data[0]);
//
//            Application_Model_ArrayToXLSX::toXLSX($data, $columnNames);
//            die();
            
            
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
        }

    }

<?php


    class Application_Model_ArrayToXLSX {
        
		/**
		 * Application_Model_ArrayToXLSX::toXLSX($data, $columnNames);
		 * 
		 * Accepts 2 arrays (column names and columns data). Result is generated .xmls on server or on client side.
		 * To choosee on which side to generate this look at the commented code.
		 * Really important code is from line 26 which generates excel header from columns names. (A1 -> id, ...., AC -> name)
		 * To work properly $columnNames should be made like this: $columnNames = array_keys($data[0]);
		 * @param type $dataArray
		 * @param type $columnNames
		 */
        public function toXLSX($dataArray, $columnNames = NULL) {
                    
            define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            date_default_timezone_set('Europe/London');
            require_once  'Classes/PHPExcel.php';

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            if (!is_array($columnNames)) {
                $columnNames = array_shift($dataArray);
            }

			//This code creates excel headers
			$s = 1; 
			$prefix = '';
			foreach ($columnNames as $value) {
				$pom = floor(($s-1) / 26);
				if ($pom > 0) {
					$prefix = $pom + 64;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($prefix) . chr((($s-1) % 26) + 65) . '1', $value);				
				}
				else {
					$objPHPExcel->getActiveSheet()->setCellValue(chr((($s-1) % 26) + 65) . '1', $value);
				}
				
				$s++;
			}
			

			//$s = 1;
			//$prefix = '';
			//foreach ($columnNames as $value) {
			//	$prefix = (floor(($s - 1) / 26) + 64) == 0 ? '' : chr(floor(($s - 1) / 26) + 64);
			//	$objPHPExcel->getActiveSheet()->setCellValue($prefix . chr((($s - 1) % 26) + 65) . '1', $value);
			//	$s++;
			//}
			
//            $objPHPExcel->getActiveSheet()
//                    ->setCellValue('A1', 'Year')
//                    ->setCellValue('B1', 'Quarter')
//                    ->setCellValue('C1', 'Country')
//                    ->setCellValue('D1', 'Sales');

            //
            //$dataArray = Application_Model_ArrayToXLSX::getData();

            $objPHPExcel->getActiveSheet()->fromArray($dataArray, NULL, 'A2');
            $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
            $objPHPExcel->setActiveSheetIndex(0);

            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=\"results.xlsx\"");
            header("Cache-Control: max-age=0");
            
            
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();
            $objWriter->save('php://output');
            
            //$objWriter->save(str_replace('.php', '.xlsx', __FILE__ ));


            echo date('H:i:s'), " File written to ", str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)), EOL;

        }
        
        
        function getData() {
            return array(
                array('2010', 'Q1', 'United States', 790,),
                array('2010', 'Q2', 'United States', 730),
                array('2010', 'Q3', 'United States', 860),
                array('2010', 'Q4', 'United States', 850),
                array('2011', 'Q1', 'United States', 800),
                array('2011', 'Q2', 'United States', 700),
                array('2011', 'Q3', 'United States', 900),
                array('2011', 'Q4', 'United States', 950),
                array('2010', 'Q1', 'Belgium', 380),
                array('2010', 'Q2', 'Belgium', 390),
                array('2010', 'Q3', 'Belgium', 420),
                array('2010', 'Q4', 'Belgium', 460),
                array('2011', 'Q1', 'Belgium', 400),
                array('2011', 'Q2', 'Belgium', 350),
                array('2011', 'Q3', 'Belgium', 450),
                array('2011', 'Q4', 'Belgium', 500),
                array('2010', 'Q1', 'UK', 690),
                array('2010', 'Q2', 'UK', 610),
                array('2010', 'Q3', 'UK', 620),
                array('2010', 'Q4', 'UK', 600),
                array('2011', 'Q1', 'UK', 720),
                array('2011', 'Q2', 'UK', 650),
                array('2011', 'Q3', 'UK', 580),
                array('2011', 'Q4', 'UK', 510),
                array('2010', 'Q1', 'France', 510),
                array('2010', 'Q2', 'France', 490),
                array('2010', 'Q3', 'France', 460),
                array('2010', 'Q4', 'France', 590),
                array('2011', 'Q1', 'France', 620),
                array('2011', 'Q2', 'France', 650),
                array('2011', 'Q3', 'France', 415),
                array('2011', 'Q4', 'France', 570),
                array('2010', 'Q1', 'Germany', 720),
                array('2010', 'Q2', 'Germany', 680),
                array('2010', 'Q3', 'Germany', 640),
                array('2010', 'Q4', 'Germany', 660),
                array('2011', 'Q1', 'Germany', 680),
                array('2011', 'Q2', 'Germany', 620),
                array('2011', 'Q3', 'Germany', 710),
                array('2011', 'Q4', 'Germany', 690),
                array('2010', 'Q1', 'Spain', 510),
                array('2010', 'Q2', 'Spain', 490),
                array('2010', 'Q3', 'Spain', 470),
                array('2010', 'Q4', 'Spain', 420),
                array('2011', 'Q1', 'Spain', 460),
                array('2011', 'Q2', 'Spain', 390),
                array('2011', 'Q3', 'Spain', 430),
                array('2011', 'Q4', 'Spain', 415),
                array('2010', 'Q1', 'Italy', 440),
                array('2010', 'Q2', 'Italy', 410),
                array('2010', 'Q3', 'Italy', 420),
                array('2010', 'Q4', 'Italy', 450),
                array('2011', 'Q1', 'Italy', 430),
                array('2011', 'Q2', 'Italy', 370),
                array('2011', 'Q3', 'Italy', 350),
                array('2011', 'Q4', 'Italy', 335),
            );
        }
            
    }
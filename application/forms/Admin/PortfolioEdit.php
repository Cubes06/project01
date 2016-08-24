
<?php

    class Application_Form_Admin_PortfolioEdit extends Zend_Form {

        
        public function init() {
            
            $title = new Zend_Form_Element_Text('title');
            $title->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                    ->setRequired(true);
            $this->addElement($title);
            
            
            $cmsPorfolioCategoriesDbTable = new Application_Model_DbTable_CmsPortfoliosCategories();
            $portfoliosCategories = $cmsPorfolioCategoriesDbTable->search(array(
                'filters' => array(
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $data = array();
            foreach ($portfoliosCategories as $portfoliosCategory) {
                $data[$portfoliosCategory['id']] = $portfoliosCategory['name']; 
            }
            $dataCategories = new Zend_Form_Element_Multiselect('data_categories');
            $dataCategories->addMultiOptions($data)->setRequired(true);
            $this->addElement($dataCategories);
            


            $characteristic1 = new Zend_Form_Element_Text('characteristic1');
            $characteristic1->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                    ->setRequired(false);
            $this->addElement($characteristic1);

            $characteristic2 = new Zend_Form_Element_Text('characteristic2');
            $characteristic2->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                    ->setRequired(false);
            $this->addElement($characteristic2);

            $description = new Zend_Form_Element_Textarea('description');
            $description->addFilter('StringTrim')
                    ->setRequired(false);
            $this->addElement($description);


            $portfolioPhoto = new Zend_Form_Element_File('portfolio_photo');
            $portfolioPhoto->setRequired(false);

            $this->addElement($portfolioPhoto);

        }//endf init

        
        public function populate(array $values){
            if (isset($values['data_categories']) && !is_array($values['data_categories'])) {
                $values['data_categories'] = explode(', ', $values['data_categories']);
            }
            return parent::populate($values);
        }
        
        
        public function getValues($suppressArrayNotation = false) {
            $values = parent::getValues($suppressArrayNotation);
            $values['data_categories'] = implode(', ', $values['data_categories']);
            return $values;
        }

} //end of: class Application_Form_Admin_MemberEdit
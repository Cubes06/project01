
<?php

    class Application_Form_Admin_PortfolioEdit extends Zend_Form {

        
        public function init() {
            
            $title = new Zend_Form_Element_Text('title');
            $title->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                    ->setRequired(true);
            $this->addElement($title);
            

            $dataCategories = new Zend_Form_Element_Text('data_categories');
            $dataCategories->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('max' => 255))
                    ->setRequired(true);
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


    } //end of: class Application_Form_Admin_MemberEdit
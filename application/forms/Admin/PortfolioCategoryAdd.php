<?php

    class Application_Form_Admin_PortfolioCategoryAdd extends Zend_Form {

        
        public function init() {
            
            $name = new Zend_Form_Element_Text('name');
            $name->addFilter('StringTrim')
                    ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                    ->setRequired(true);
            $this->addElement($name);

            
            $description = new Zend_Form_Element_Textarea('description');
            $description->addFilter('StringTrim')
                    ->setRequired(false);
            $this->addElement($description);


        }//endf init


    } //end of: class Application_Form_Admin_MemberAdd
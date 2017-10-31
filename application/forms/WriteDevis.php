<?php

class Application_Form_WriteDevis extends Zend_Form
{

    public function init()
    {
        $text = new Zend_Form_Element_Textarea('redactionDevis');
        $text->setAttribs(array('class'=>'form-control rte', 'aria-describedby'=>'sizing-addon2', 'id'=>'redactionDevis'));
        
        $this->addElement($text);
        
        $valid = new Zend_Form_Element_Button('validerRedactionDevis');
        $valid->setLabel('Valider');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($valid);
    }


}


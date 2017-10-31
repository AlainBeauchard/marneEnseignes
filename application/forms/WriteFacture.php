<?php

class Application_Form_WriteFacture extends Zend_Form
{

    public function init()
    {
        $text = new Zend_Form_Element_Textarea('redactionFacture');
        $text->setAttribs(array('class'=>'form-control rte', 'aria-describedby'=>'sizing-addon2', 'id'=>'redactionFacture'));
        
        $this->addElement($text);
        
        $valid = new Zend_Form_Element_Button('validerRedactionFacture');
        $valid->setLabel('Valider rédaction');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($valid);
    }


}


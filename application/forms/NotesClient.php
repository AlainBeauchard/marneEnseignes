<?php

class Application_Form_NotesClient extends Zend_Form
{

    public function init()
    {
	    $id_client = new Zend_Form_Element_Hidden('id_client');
        $this->addElement($id_client);
        
        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel("Notes");
        $note->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'id'=>'redactionDevis'));
        $this->addElement($note);
        
        
        $valid = new Zend_Form_Element_Button('valider');
        $valid->setLabel('Valider rédaction');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'aria-describedby'=>'sizing-addon2', 'type'=>'submit'));
        $this->addElement($valid);
    }


}


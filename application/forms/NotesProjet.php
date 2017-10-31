<?php

class Application_Form_NotesProjet extends Zend_Form
{

    public function init()
    {
	    $id_projet = new Zend_Form_Element_Hidden('id_projet');
        $this->addElement($id_projet);
        
        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel("Notes");
        $note->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'rows'=>5));
        $this->addElement($note);
        
        
        $valid = new Zend_Form_Element_Button('validerNotes');
        $valid->setLabel('Valider rédaction');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'aria-describedby'=>'sizing-addon2', 'type'=>'submit'));
        $this->addElement($valid);
    }


}


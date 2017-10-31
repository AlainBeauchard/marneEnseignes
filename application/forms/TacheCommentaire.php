<?php

class Application_Form_TacheCommentaire extends Zend_Form
{

    public function init()
    {
        $commentaire = new Zend_Form_Element_Text('commentaire');
        $commentaire->setAttribs(array('placeholder'=>'Commentaire','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $commentaire->setRequired(true);
        $this->addElement($commentaire);
        
        $valid = new Zend_Form_Element_Button('valider');
        $valid->setLabel('Valider');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit', 'id'=>'addNoteTache'));
        $this->addElement($valid);
    }


}


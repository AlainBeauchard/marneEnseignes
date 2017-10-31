<?php

class Application_Form_EditeNote extends Zend_Form
{

    public function init()
    {
		$idNote = new Zend_Form_Element_Hidden('id');
	    $this->addElement($idNote);

    	$editenote = new Zend_Form_Element_Textarea('note');
		$editenote->setLabel('Note:')
		    ->setRequired(true)
		    ->setAttrib('ROWS', '5');
		$editenote->setAttribs(array('placeholder'=>'Note','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));  

		$this->addElement($editenote);
	    
	    
	    $ajouter = new Zend_Form_Element_Button('Sauver');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit', 'id'=>'editeNote'));
        $ajouter->addDecorator(array('last'=>'HtmlTag', array('tag'=>'div', 'class' => 'pippo'))) ;
        $this->addElement($ajouter);


		$annuler = new Zend_Form_Element_Button('Annuler');
        $annuler->setAttribs(array('class'=>'btn btn-danger', 'type'=>'button', 'id'=>'btAnnuleEditNote'));
        $annuler->addDecorator(array('last'=>'HtmlTag', array('tag'=>'div', 'class' => 'pippo'))) ;
        
        $this->addElement($annuler);

    }


}


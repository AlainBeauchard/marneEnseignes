<?php

class Application_Form_Plantache extends Zend_Form
{

    public function init()
    {
	    $listePriority = [0=>"Priorité",1=>"!",2=>"!!",3=>"!!!"];
	    
	    $tache = new Zend_Form_Element_Hidden('id_tache');
	    $this->addElement($tache);
	    
	    $user = new Zend_Form_Element_Hidden('id_user');
	    $this->addElement($user);
	    
        $date = new ZendX_JQuery_Form_Element_DatePicker('date');
        $date->setAttribs(array('placeholder'=>'Date','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $date->setJQueryParam('dateFormat', 'dd/mm/yy');
        $date->setValue(date('d/m/Y'));
        $date->setRequired(true);
        $this->addElement($date);
        
        $nbh = new Zend_Form_Element_Text('nb_heures');
	    $nbh->setAttribs(array('placeholder'=>'nb heures','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $this->addElement($nbh);
	    
		$priority = new Zend_Form_Element_Select('priority');
		$priority->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$priority->addMultiOptions($listePriority);
		$this->addElement($priority);
	    
	    
	    $ajouter = new Zend_Form_Element_Button('ajouter');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'add_tache_event'));
        $this->addElement($ajouter);
    }


}


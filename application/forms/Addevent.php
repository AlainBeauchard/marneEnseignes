<?php

class Application_Form_Addevent extends Zend_Form
{
	private $_dates;
	
	public function __construct ($dates){
		
		$this->_dates = $dates;
		$this->init();
		
	}

    public function init()
    {
	    foreach($this->_dates as $date){
		    list($j,$m,$a) = explode('/', $date);
		    $liste[$a.'-'.$m.'-'.$j] = $date;
	    }
	    
	    $db_users = new Application_Model_Users();
	    $users = $db_users->fetchAll();
	    foreach($users as $user){
		    $listeUsers[$user->id_user] = $user->nom;
	    }
	    
	    $listePriority = [0=>"Priorité",1=>"!",2=>"!!",3=>"!!!"];
	    
	    $selDates = new Zend_Form_Element_Select('date');
	    $selDates->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $selDates->addMultiOption(0, 'Date');
	    $selDates->addMultiOptions($liste);
	    $this->addElement($selDates);
	    
		$tache = new ZendX_JQuery_Form_Element_AutoComplete('event');
        $tache->setAttribs(array('placeholder'=>'Ajouter une tâche','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $tache->setJQueryParams(array('source' => '/ajax/taches', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.value)}')));
        $tache->setRequired(true);
        $this->addElement($tache);
	    
	    $nbh = new Zend_Form_Element_Text('nb_heures');
	    $nbh->setAttribs(array('placeholder'=>'nb heures','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
	    $this->addElement($nbh);
	    
	    $priority = new Zend_Form_Element_Select('priority');
		$priority->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$priority->addMultiOptions($listePriority);
		$this->addElement($priority);
	    
	    $selectUsers = new Zend_Form_Element_Select('id_user1');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Affecter une personne");
		$selectUsers->addMultiOptions($listeUsers);
		$this->addElement($selectUsers);
		
		$ajouter = new Zend_Form_Element_Button('ajouter');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'add_event'));
        $this->addElement($ajouter);
       
    }


}


<?php

class Application_Form_AjoutTache extends Zend_Form
{

    public function init()
    {
	    $db_users = new Application_Model_Users();
	    $users = $db_users->fetchAll();
	    foreach($users as $user){
		    $listeUsers[$user->id_user] = $user->nom;
	    }
	    
	    $id_projet = new Zend_Form_Element_Hidden('id_projet');
	    $this->addElement($id_projet);
	    
	    $id_tache = new Zend_Form_Element_Hidden('id_tache');
	    $this->addElement($id_tache);
		
		$tache = new ZendX_JQuery_Form_Element_AutoComplete('tache');
        $tache->setAttribs(array('placeholder'=>'Ajouter une tâche','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $tache->setJQueryParams(array('source' => '/ajax/taches', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.value)}')));
        $tache->setRequired(true);
        $this->addElement($tache);
		
		$selectUsers = new Zend_Form_Element_Select('id_user1');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Affecter une personne");
		$selectUsers->addMultiOptions($listeUsers);
		$this->addElement($selectUsers);
		
		$selectUsers = new Zend_Form_Element_Select('id_user2');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Affecter une personne");
		$selectUsers->addMultiOptions($listeUsers);
		$this->addElement($selectUsers);
		
		$selectUsers = new Zend_Form_Element_Select('id_user3');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Affecter une personne");
		$selectUsers->addMultiOptions($listeUsers);
		$this->addElement($selectUsers);
		
		$duree = new Zend_Form_Element_Text('duree');
		$duree->setAttribs(array('placeholder'=>'Nombre de jours','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$this->addElement($duree);

        $visible = new Zend_Form_Element_Checkbox('visible');
        $visible->setAttribs(array('placeholder'=>'Tâche visible','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $visible->setChecked(true);
        $this->addElement($visible);

		$ajouter = new Zend_Form_Element_Button('ajouter');
        $ajouter->setLabel('Ajouter');
        $ajouter->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'ajouter_tache'));
        $this->addElement($ajouter);
    }


}


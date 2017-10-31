<?php

class Application_Form_SelectUser extends Zend_Form
{

    public function init()
    {
	    $db_users = new Application_Model_Users();
	    $users = $db_users->fetchAll();
	    foreach($users as $user){
		    $listeUsers[$user->id_user] = $user->nom;
	    }
	    
        $selectUsers = new Zend_Form_Element_Select('selectUser');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Afficher une personne");
		$selectUsers->addMultiOptions($listeUsers);
		$this->addElement($selectUsers);
    }


}


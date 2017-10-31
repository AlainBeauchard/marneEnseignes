<?php

class Application_Form_Messages extends Zend_Form
{

    public function init()
    {
	   	$db_users = new Application_Model_Users();
	    $users = $db_users->fetchAll();
	    foreach($users as $user){
		    $listeUsers[$user->id_user] = $user->nom;
	    }
	    
		$reponse = new Zend_Form_Element_Hidden('reponse');
        $this->addElement($reponse);
	    
        $titre = new Zend_Form_Element_Text('titre');
        $titre->setAttribs(array('placeholder'=>'Titre du message','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($titre);
        
        $dossier = new Zend_Form_Element_Text('dossier');
        $dossier->setAttribs(array('placeholder'=>'N° de dossier','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($dossier);
        
        $message = new Zend_Form_Element_Textarea('message');
        $message->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $this->addElement($message);
        
        $selectUsers = new Zend_Form_Element_Select('id_destinataire');
		$selectUsers->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
		$selectUsers->addMultiOption(0, "Destinataire");
		$selectUsers->addMultiOptions($listeUsers);
		$selectUsers->setDescription("Si aucun destinataire n'est choisi, le message est envoyé à tout le monde.");
		$this->addElement($selectUsers);
		
        $dest = new Zend_Form_Element_Button('ajouterDest');
        $dest->setLabel('Ajouter un destinataire');
        $dest->setAttribs(array('class'=>'btn btn-primary', 'type'=>'button', 'id'=>'btAddDest'));
        $this->addElement($dest);
		
		$valid = new Zend_Form_Element_Button('envoyermessage');
        $valid->setLabel('Envoyer message');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }


}


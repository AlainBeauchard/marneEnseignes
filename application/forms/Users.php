<?php

class Application_Form_Users extends Zend_Form
{

    public function init()
    {
	    $auth = Zend_Auth::getInstance();
	    
        $nom = new Zend_Form_Element_Text('nom');
        $nom->setAttribs(array('placeholder'=>'Nom','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $nom->setRequired(true);
        $this->addElement($nom);
        
        $login = new Zend_Form_Element_Text('login');
        $login->setAttribs(array('placeholder'=>'Login','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $login->setRequired(true);
        $this->addElement($login);
        
        $mdp = new Zend_Form_Element_Password('mdp');
        $mdp->setAttribs(array('placeholder'=>'Mot de passe','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $mdp->setRequired(true);
        $this->addElement($mdp);

        $color = new Zend_Form_Element_Text('color');
        $color->setAttribs(array('placeholder'=>'couleur','class'=>'form-control jscolor', 'aria-describedby'=>'sizing-addon2', 'onchange' => 'updateColor(this.jscolor)'));
        $color->setRequired(true);
        $this->addElement($color);

        if($auth->getIdentity()->status == 'admin'){
            $status = new Zend_Form_Element_Select('status');
			$status->setAttribs(array('class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
			$status->addMultiOption('user', 'Utilisateur');
			$status->addMultiOption('admin', 'Administrateur');
			$this->addElement($status);
        }

        $valid = new Zend_Form_Element_Button('valider');
        $valid->setLabel('Valider');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);

    }


}


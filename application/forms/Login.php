<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $login = new Zend_Form_Element_Text('login');
        $login->setAttribs(array('placeholder'=>'Login','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $login->setRequired(true);
        $this->addElement($login);
        
        $mdp = new Zend_Form_Element_Password('mdp');
        $mdp->setAttribs(array('placeholder'=>'Mot de passe','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $mdp->setRequired(true);
        $this->addElement($mdp);
        
        $valid = new Zend_Form_Element_Button('Ajouter');
        $valid->setLabel('Se connecter');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        $this->addElement($valid);
    }


}


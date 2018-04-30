<?php

class Application_Form_FiltreDevis extends Zend_Form
{

    public function init()
    {
	   	$db_users = new Application_Model_Users();
	   	$users = $db_users->fetchAll();
	   	foreach($users as $user){
		   	$listeUsers[$user->id_user] = $user->nom;
	   	}
	   	
	   	$Arrmois = array('01'=>'Janvier', '02'=>'Février', '03'=>'Mars', '04'=>'Avril', '05'=>'Mai', '06'=>'Juin', '07'=>'Juillet', '08'=>'Aout', '09'=>'Septembre', '10'=>'Octobre', '11'=>'Novembre', '12'=>'Décembre');
	   	
	   	$db_devis = new Application_Model_Devis();
	   	
	   	$y1 = $db_devis->getMaxYear();
	   	$y2 = (int) date('Y');
	   	
	   	for($i = $y1; $i <= $y2; $i++){
		   	$years[$i] = $i;
	   	}
	   	
	   	$typeFiltre = new Zend_Form_Element_Hidden('type_filtre');
	    $typeFiltre->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($typeFiltre);
        
        $id_client = new Zend_Form_Element_Hidden('id_client');
        $id_client->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($id_client);
        
        $valide = new Zend_Form_Element_Hidden('valide');
        $valide->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($valide);

        $num_client = new Zend_Form_Element_Text('num_client');
		$num_client->setAttribs(array('placeholder'=>'Num client','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($num_client);

        $num_devis = new Zend_Form_Element_Text('num_devis');
		$num_devis->setAttribs(array('placeholder'=>'Num devis','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($num_devis);

        $client = new ZendX_JQuery_Form_Element_AutoComplete('client');
        $client->setAttribs(array('placeholder'=>'Société','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $client->setJQueryParams(array('source' => '/ajax/clients', 'select' => new Zend_Json_Expr('function(event,ui){$("#id_client").val(ui.item.id)}')));
        $this->addElement($client);
        
        $sem = new Zend_Form_Element_Text('sem');
        $sem->setAttribs(array('placeholder'=>'Sem','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($sem);
        
        $dossier = new Zend_Form_Element_Text('ref');
        $dossier->setAttribs(array('placeholder'=>'Dossier','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($dossier);
        
        $contact = new Zend_Form_Element_Text('contact');
        $contact->setAttribs(array('placeholder'=>'Contact','class'=>'form-control autocomplete input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($contact);
        
        $mois = new Zend_Form_Element_Select('mois');
        $mois->setAttribs(array('class'=>'form-control input-sm', 'aria-describedby'=>'sizing-addon2'));
        $mois->addMultiOption(0, 'Mois');
        $mois->addMultiOptions($Arrmois);
        $mois->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($mois);
        
        $year = new Zend_Form_Element_Select('annee');
        $year->setAttribs(array('class'=>'form-control input-sm', 'aria-describedby'=>'sizing-addon2'));
        $year->addMultiOption(0, 'Année');
        $year->addMultiOptions($years);
        $year->removeDecorator('DtDdWrapper')->removeDecorator('label')->removeDecorator('HtmlTag');
        $this->addElement($year);
        
        $users = new Zend_Form_Element_Select('users');
        $users->setAttribs(array('class'=>'form-control input-sm', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $users->addMultiOption(0, 'Select');
        $users->addMultiOptions($listeUsers);
        $this->addElement($users);
        
        $titre = new Zend_Form_Element_Text('titre');
        $titre->setAttribs(array('placeholder'=>'Titre','class'=>'form-control input-sm autocomplete', 'aria-describedby'=>'sizing-addon2'))->removeDecorator('label');
        $this->addElement($titre);
        
        $rechercher = new Zend_Form_Element_Button('rechercher');
        $rechercher->setLabel('Rechercher');
        $rechercher->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'submit'));
        $rechercher->removeDecorator('DtDdWrapper');
        $this->addElement($rechercher);
        
        $rechercherCloture = new Zend_Form_Element_Button('rechercherClotures');
        $rechercherCloture->setLabel('Rechercher');
        $rechercherCloture->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'submit'));
        $rechercherCloture->removeDecorator('DtDdWrapper');
        $this->addElement($rechercherCloture);
        
        $resetClient = new Zend_Form_Element_Reset('resetClient');
        $resetClient->setLabel('Reset');
        $resetClient->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreClient'));
        $resetClient->removeDecorator('DtDdWrapper');
        $this->addElement($resetClient);
        
        $resetProjet = new Zend_Form_Element_Reset('resetProjet');
        $resetProjet->setLabel('Reset');
        $resetProjet->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreProjet'));
        $resetProjet->removeDecorator('DtDdWrapper');
        $this->addElement($resetProjet);
        
        $resetProjetClotures = new Zend_Form_Element_Reset('resetProjetClotures');
        $resetProjetClotures->setLabel('Reset');
        $resetProjetClotures->setAttribs(array('class'=>'btn btn-primary btn-sm', 'type'=>'button', 'id'=>'resetFiltreProjetClotures'));
        $resetProjetClotures->removeDecorator('DtDdWrapper');
        $this->addElement($resetProjetClotures);
    }


}


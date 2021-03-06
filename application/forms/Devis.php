<?php

class Application_Form_Devis extends Zend_Form
{

    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
	    $this->setAttrib('role', 'form');
        //$this->addDecorators([['Description'=>['tag'=> 'p', 'class' => 'formDevis']]]);

        $codeClient = new Zend_Form_Element_Text('codeClient');
        $codeClient->setAttribs(array('placeholder'=>'code client','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeClientsDevis'));
        $codeClient->setLabel('Code Client');
        $codeClient->setRequired(true);
        $this->addElement($codeClient);

        $refDossier = new Zend_Form_Element_Text('refDossier');
        $refDossier->setAttribs(array('placeholder'=>'ref dossier','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'refDossierListe'));
        $refDossier->setLabel('Ref dossier');
        $refDossier->setRequired(true);
        $this->addElement($refDossier);

        $numDevis = new Zend_Form_Element_Text('numDevis');
        $numDevis->setAttribs(array('placeholder'=>'numéro de devis','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $numDevis->setLabel('Numéro de devis');
        $numDevis->setRequired(true);
        $this->addElement($numDevis);

        $montantTotal = new Zend_Form_Element_Text('montantTotal');
        $montantTotal->setAttribs(array('placeholder'=>'Montant total','class'=>'form-control txtEntete', 'aria-describedby'=>'sizing-addon2', 'readonly'=>true));
        $montantTotal->setLabel('Montant Total');
        $montantTotal->setRequired(false);
        $this->addElement($montantTotal);

        $intitule = new Zend_Form_Element_Text('intitule');
        $intitule->setAttribs(array('placeholder'=>'Intitule','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $intitule->setLabel('Intitulé');
        $intitule->setRequired(true);
        $this->addElement($intitule);

        $nomClient = new Zend_Form_Element_Text('nomClient');
        $nomClient->setAttribs(array('placeholder'=>'Nom du client','class'=>'form-control', 'aria-describedby'=>'sizing-addon2'));
        $nomClient->setLabel('Nom client');
        $nomClient->setRequired(true);
        $this->addElement($nomClient);

        $delaiLivraison = new Zend_Form_Element_Text('delai');
        $delaiLivraison->setAttribs(array('placeholder'=>'Delai de livraison','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listDelai'));
        $delaiLivraison->setLabel('Délai de livraison');
        $delaiLivraison->setRequired(true);
        $this->addElement($delaiLivraison);

        $modeDeReglement = new Zend_Form_Element_Text('reglement');
        $modeDeReglement->setAttribs(array('placeholder'=>'Mode de règlement','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'list' => 'listeReglement'));
        $modeDeReglement->setLabel('Mode de règlement');
        $modeDeReglement->setRequired(true);
        $this->addElement($modeDeReglement);

        $dteCreation = new Zend_Form_Element_Text('dateCreation', ['attribs' => []]);
        $dteCreation->setAttribs(array('placeholder'=>'Date création','class'=>'form-control', 'aria-describedby'=>'sizing-addon2', 'readonly' => 'true'));
        $dteCreation->setLabel('Date Création');
        $dteCreation->setRequired(true);

        $this->addElement($dteCreation);


        $valid = new Zend_Form_Element_Button('Ajouter');
        $valid->setAttribs(array('class'=>'btn btn-primary', 'type'=>'submit'));
        //$valid->addDecorators([['Description'=>['tag'=>'p', 'class' => 'mesBoutons']]]);
        $this->addElement($valid);
    }

    /**
     * @return int|string
     */
    function getDevisNewNum(){
	    //TODO attention ceci est le devis initial : pas le mien !
	    $db_devis = new Application_Model_Devis();
	    $select = $db_devis->select()->from('devis', array('max(ref) as max'));
	    $newNumdevis = $db_devis->fetchRow($select);
	    
	    return $newNumdevis->max + 1;
	    
    }
}


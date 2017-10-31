<?php

class Application_Form_SearchClient extends Zend_Form
{

    public function init()
    {
    	$client = new ZendX_JQuery_Form_Element_AutoComplete('client');
        $client->setAttribs(array('placeholder'=>'Client','class'=>'form-control autocomplete', 'aria-describedby'=>'sizing-addon2'));
        $client->setJQueryParams(array('source' => '/ajax/clients', 'select' => new Zend_Json_Expr('function(event,ui){window.location.href="/clients/editer/id/" + ui.item.id}')));
        $this->addElement($client);
    	
    }


}


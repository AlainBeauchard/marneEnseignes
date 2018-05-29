var masckPickColor = true;
var masckVisibleTache = true;
var $elemCode = null;
var nomTableGlobal = null;
var indiceLigneGlobal = null;

var tabColor = ['blue','lightBlue','green', 'lightgreen', 'orange', 'darkorange', 'red', 'redVif', 'yellow', 'lemon', 'marron', 'gray', 'violet', 'none'];

function updateColor(jscolor)
{
	console.log(" update Color " + jscolor);
	// 'jscolor' instance can be used as a string
    setTimeout($('#color').val( '#' + jscolor), 500);
}

function fctDisapiredColor()
{
	if(masckPickColor)
		$("#plaletteCouleur").addClass('noDisplay');
    if(masckVisibleTache)
        $("#divVisibleTache").addClass('noDisplay');

}

function suppAllClassColorElem($elem)
{
    for(var i=0;i<tabColor.length;i++)
        $elem.removeClass(tabColor[i]);
}

function fctToggleBoutonTachesMasquees(dataVisible)
{
    //On masque tous le bouton concerné dans la popIn !
    $("[name='btRendTache']").removeClass('noDisplay');
    $("[name='btRendTache'][data-rendvisible="+dataVisible+"]").addClass('noDisplay');
   /*
    $("[name='btAfficheTachesMasquees']").each(function()
        {
            if($(this).attr("data-mode")!== dataVisible) {
                $(this).removeClass('active').attr('aria-pressed','false');
            } else {
                $(this).addClass('active').attr('aria-pressed','true');
            }
        }
    );
    */
}

$(document).ready(function(){
    $(document).on('click', fctDisapiredColor);

    var $idPreviousProjet = $("#idPreviousProjet");
    if ($idPreviousProjet.length)
    	localStorage.setItem('currentLine', 'projet_'+$idPreviousProjet.val());

	$("#listeProjets").on('scroll', fctDisapiredColor);

	$("#codeClient").unbind('keyup').bind('keyup', fctRemplitListeClient);

	$("#refDossier").unbind('keyup').bind('keyup', fctRemplitRefDossier);

	$("#btDevisRecord").unbind('click').bind('click', fctFillFieldsCollapse);

	$(".changeColor").click(function()
		{
			var classToAdd = getClassToAdd( $(this) );
			console.log(classToAdd);
			if( classToAdd === "none" ) {
                $.post('/ajax/changeColorProjet',
                    {
                        id_projet  : $("#id_projet_pc").val(),
                        colorclass : ''
                    },
                    function(){
                        suppAllClassColorElem($("#projet_" + $('#id_projet_pc').val()));
                    }
                )
            } else {
				$.post('/ajax/changeColorProjet',
					{
						id_projet  : $("#id_projet_pc").val() ,
						colorclass : classToAdd
					},
					function(){
						suppAllClassColorElem( $("#projet_"+$('#id_projet_pc').val()) );
						$("#projet_"+$("#id_projet_pc").val()).addClass(classToAdd);
					}
				)
			}
		});

	//On place bien la div sous la nav bar
	//$("#divPrincipale").css('margin-top', ($("#topNavBar").height() - 120)+"px" );

// 	$('.table-fixed').stickyTableHeaders();

	$('.sur').click(function(){
		if(confirm('Etes-vous sur ?')){
			return true;
		}else{
			return false;
		}
	})
	
	$('#print_fiche').click(function(){
		window.print();
	})
	
	$("#btAddDest").click( function()
	{
		$varElem = $("#id_destinataire");
		$select = "<select name='id_destinataire_plus[]' id='id_destinataire_"+$varElem.length+"'class='form-control' aria-describedby='sizing-addon2' >"+$("#id_destinataire").html()+"</select>";
		$("#id_destinataire-element").html( $("#id_destinataire-element").html() + $select );
	});

	if( $("#Sauver-label").length )
	{	
		$("#Sauver-label").parent().css('float','left');
		$("#Annuler-label").parent().css('float','right');
	}
// 	$('.checkBrightness').colourBrightness();

	$("#btAnnuleEditNote").click(function()
		{
			document.forms['formRetour'].submit();
		});

	$('#listeProjets > tr').click(function(){
		var currentLine = $(this).attr('id');
		localStorage.setItem('currentLine', currentLine);
		
		$('#listeProjets > tr').css('border', 'none');
		$(this).css('border', '3px solid orange');
	});

	$('#listeProduits > tr').click(function(){
		var currentLine = $(this).attr('id');
		localStorage.setItem('currentLine', currentLine);

		$('#listeProduits > tr').css('border', 'none');
		$(this).css('border', '3px solid orange');
	});

	$('#listeFactures > tr').click(function(){
		var currentLine = $(this).attr('id');
		localStorage.setItem('currentLine', currentLine);

		$('#listeFactures > tr').css('border', 'none');
		$(this).css('border', '3px solid orange');
	});

	$('#liste_produits > tr').click(function(){
		var currentLine = $(this).attr('id');
		localStorage.setItem('currentLine', currentLine);

		$('#liste_produits > tr').css('border', 'none');
		$(this).css('border', '3px solid orange');
	});

	$('#newMessageAlert').css('display','none');

	$.post('/ajax/getnewmessage',{},
		function(nb){
			if (nb > 0){
				$('#newMessageAlert').css('display','block');
			}
		}
	)
	
	$('#loader').css('display', 'none');
	$('#dialog_nom_modele').css('display','none');
	
	$('#sorter').tablesorter({
		debug: true
	});
	
	$('.sortable').sortable({
		update: function(event, ui) {
	        var info = $(this).sortable("toArray");
			$.post('/ajax/updatesort',{
				list: info
			}, function(){
				
			});
	    }
	});
	
	$('.draggable').draggable({
		connectToSortable: ".sortable",
		cursor: "crosshair"
	});
	
	$('.droppable').droppable(
		{
			activeClass: "ui-state-default",
			drop: function( event, ui ) {
				var id_tache = ui.helper[0].id.split('_')[1];
				var new_date = $(event.target).attr('id');

				$.post('/ajax/moveevent',
				{
					id_tache: id_tache,
					new_date: new_date
				}, function(){
					

				});
      		}
		}
	);
	
	/***** Clients *****/
	
	$('#code_client').blur(function(){
		var codeclient = $(this).val();

		$.getJSON('/ajax/codeclient',{
			codeclient: codeclient
		}, function(json){
			
// 			alert(json[0].datas.id_client)

			$('#id_client').val(json[0].datas.id_client);
			$('#client').val(json[0].datas.societe);
			$('#nomlivraison').val(json[0].datas.societe);
			$('#adresselivraison').val(json[0].datas.adresse + " " + json.adresse2);
			$('#codepostal').val(json[0].datas.codepostal);
			$('#ville').val(json[0].datas.ville);
			$('#contact_nom').val(json[0].datas.contact_nom);
			$('#contact_mail').val(json[0].datas.contact_mail+", " + json[0].datas.mel);
			$('#contact_tel').val(json[0].datas.contact_tel);
			
			
		}, 'json')
	});
	
	/****** RTE ******/	
	
	$('textarea.rte').tinymce({
		theme: 'modern',
		language_url: '/js/tinymce/langs/fr_FR.js',
        selector: "textarea",
        height: 450,
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
        ],

        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft",
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'],

        menubar: true,
        toolbar_items_size: 'small',

        style_formats: [{
            title: 'Bold text',
            inline: 'b'
        }, {
            title: 'Red text',
            inline: 'span',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Red header',
            block: 'h1',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Example 1',
            inline: 'span',
            classes: 'example1'
        }, {
            title: 'Example 2',
            inline: 'span',
            classes: 'example2'
        }, {
            title: 'Table styles'
        }, {
            title: 'Table row 1',
            selector: 'tr',
            classes: 'tablerow1'
        }],

        templates: [{
            title: 'Test template 1',
            content: 'Test 1'
        }, {
            title: 'Test template 2',
            content: 'Test 2'
        }],

        init_instance_callback: function () {
            window.setTimeout(function() {
                $("#div").show();
            }, 1000);
        }
	});
	
	$("#responseMessage").submit(function(){
	    var editor_id = $(this).attr('id');
	    var editor = tinymce.get(editor_id);
// 	    editor.setContent($(this).val()).save();
		$('textarea.rte').val(tinyMCE.activeEditor.getContent());
	});	
	
	$('#save_modele').click(function(){

		$('#dialog_nom_modele').dialog({
			width:600,
			title: "Nom du modèle"
		});
	});
	
	$('#saveModeleWithName').click(function(){
		var nom = $('#nom_modele').val();
		
		$('#dialog_nom_modele').dialog("close");
		
		if(nom != ''){
			$.post('/ajax/savemodele',{
				nom: nom,
				contenu: tinyMCE.activeEditor.getContent()
			}, function(r){
				
				$('#liste_nom_modeles').empty();
				$('#liste_nom_modeles').append(r);
				
			})
		}
		

	});

	$("[data-lienDossier='LienNumDossier']").each(function () {
		$(this).click(function (){
            localStorage.setItem('currentLine', 'projet_'+$(this).attr('data-idProjet') );
            window.location = '/projets/index?dr='+$(this).attr('data-typeProjet');
        });
    });

	/* Navigation */

	$('button[id^="changeColor_projets_"]').click(function()
		{
			masckPickColor = false;
			$("#id_projet_pc").val( $(this).attr('id').split('_')[2] );

			var $elem = $("#plaletteCouleur");
			$elem.css('position','absolute');
			$elem.css('z-index',100);
			$elem.css('top',$(this).offset().top - 185 );
			$elem.css('left',$(this).offset().left - 50 );
			$elem.removeClass('noDisplay');

			setTimeout( function(){masckPickColor = true;}, 500  );

		});

	if ($("#numJourLocalStorage").length > 0) {
		var numJour = localStorage.getItem('calendarDayNum');
        var $elemCol = $("[data-col='1']");
        if(numJour<4)
		{
			for(var i=0; i<numJour; i++) {
				$elemCol.each(function(indice)
				{
					var $dataNumCol = $(this).attr("data-numCol");
					if( $dataNumCol < (numJour) )
						$(this).addClass("noDisplay");
					if( $dataNumCol>4 && $dataNumCol < parseInt(numJour,10)+5 )
						$(this).removeClass("noDisplay");
				});
			}
		}
	}

	$('button[id^="gotoDay_"]').click(function(){
		var mode = $(this).attr('id').split('_')[1];
		var w = $(this).attr('id').split('_')[2];
		var $numJour = $("#numJour");
		var numJour = $numJour.val();

		var $elemCol = $("[data-col='1']");

		if( mode === "more" )
		{
			if( numJour < 4 )
			{
				$elemCol.each(function(indice)
				{
                    var $dataNumCol = $(this).attr("data-numCol");
					if( $dataNumCol == (numJour) )
						$(this).addClass("noDisplay");
					if( $dataNumCol == parseInt(numJour,10)+5 )
						$(this).removeClass("noDisplay");
				});

				$numJour.val( parseInt($numJour.val(),10) +1 );
                localStorage.setItem('calendarDayNum', $numJour.val());
			}
			else
			{
				$numJour.val(0);
                localStorage.setItem('calendarDayNum', 0);
				window.location.href = "/calendar/index/week/" + w+"?numJour=0";
			}
		}	
		else
		{
			if( numJour > 0 )
			{
				$elemCol.each(function(indice)
				{
                    var $dataNumCol = $(this).attr("data-numCol");
					if( $dataNumCol == parseInt(numJour,10)+4 )
						$(this).addClass("noDisplay");
					if( $dataNumCol == (numJour-1) )
						$(this).removeClass("noDisplay");
				});
				$numJour.val( parseInt($numJour.val(),10)-1 );
                localStorage.setItem('calendarDayNum', $numJour.val());
			}
			else
			{
				$numJour.val(4);
                localStorage.setItem('calendarDayNum', 4);
				window.location.href = "/calendar/index/week/" + w+"?numJour=2";
			}
		}
	});
	
	$('button[id^="preview_"]').click(function(){
		var id = $(this).attr('id').split('_')[2];
		var modele = $(this).attr('id').split('_')[1];
		window.open('/facturation/print/id/' + id + '/modele/' + modele + '','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=600');
		
	});
	
	$('#cgv').click(function(){
		window.open('/facturation/cgv','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=600');
		
	});
	
	$('button[id^="facturer_"]').click(function(){
		var id = $(this).attr('id').split('_')[1];
		window.location.href = "/facturation/facturer/id/" + id;
	});
	
	$('button[id^="accepter_"]').click(function(){
		var id = $(this).attr('id').split('_')[1];
		window.location.href = "/facturation/accepter/id/" + id;
	});
	
	$('button[id^="fiche_"]').click(function(){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];

		var idPreviousProjet = -1;

		if( $("#idPreviousProject_"+id).length )
			idPreviousProjet = $("#idPreviousProject_"+id).val();

		window.location.href = "/" + ctrl + "/fiche/id/" + id+"/idPrevious/"+idPreviousProjet;
	});
	
	$('button[id^="edit_"]').click(function(){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];
		window.location.href = "/" + ctrl + "/editer/id/" + id;
	});
	
	$('button[id^="switch_"]').click(function(){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];

		if( $(this).html().indexOf("livré")<0 )
			localStorage.setItem('currentLine', 'projet_'+id );
		else
			localStorage.setItem('currentLine', 'projet_'+$("#idPreviousProjet").val());

		window.location.href = "/" + ctrl + "/switch/id/" + id;
	});
	
	$('button[id^="switchDr_"]').click(function(){
		var $this = $(this);
		var id = $(this).attr('id').split('_')[2];
		$.post('/ajax/switchToDevisARelancer',{
				id_projet: id 
			}, function(r){
            	localStorage.setItem('currentLine', 'projet_'+$("#idPreviousProjet").val());
				if( $this.html().indexOf("en cours")<0 )
					$this.html("Marquer comme projet en cours");
				else
					$this.html("<span class='glyphicon glyphicon-ok' aria-hidden='true'>&nbsp;Marquer comme Devis à relancer</span>");
				
			});
	});

    $('button[id^="switchEt_"]').click(function(){
        var $this = $(this);
        var id = $(this).attr('id').split('_')[2];
        $.post('/ajax/switchToEnAttente',{
            id_projet: id
        }, function(r){
            localStorage.setItem('currentLine', 'projet_'+$("#idPreviousProjet").val());
            if( $this.html().indexOf("en cours")<0 )
                $this.html("Marquer comme projet en cours");
            else
                $this.html("<span class='glyphicon glyphicon-ok' aria-hidden='true'>&nbsp;Marquer comme Dossier en Attente</span>");

        });
    });

	/* $$$$$$$ Devis $$$$$$$$$ */
	
	$('#ref').blur(function(){
		if($('#ref').val() != ''){
			$.post('/ajax/getinfos',{
				dossier: $('#ref').val() 
			}, function(r){
				var infos = r.split('|||');
				$('#id_client').val(infos[1]);
				$('#client').val(infos[2]);
				$('#titre').val(infos[0]);
				$('#delai').val(infos[3]);
			});
		}
		
	});
	
	$('#view_deplacement').click(function(){
		$('#deplacement').dialog({
			title: 'Déplacements',
			width: 600,
			height: 280
		});
	});
	
	$('#close_depl').click(function(){
		$('#deplacement').dialog("close");
	});
	
	/**** Calculs déplacement ****/
	
	$('#qte_heure').blur(function(){
		updateRowDeplHeure()
	});
	
	$('#cout_heure').blur(function(){
		updateRowDeplHeure()
	});
	
	$('#nb_jours_heure').blur(function(){
		updateRowDeplHeure()
	});
	
	$('#qte_km').blur(function(){
		updateRowDeplKm()
	});
	
	$('#cout_km').blur(function(){
		updateRowDeplKm()
	});
	
	$('#nb_jours_km').blur(function(){
		updateRowDeplKm()
	});
	
	$('#qte_resto').blur(function(){
		updateRowDeplResto()
	});
	
	$('#cout_resto').blur(function(){
		updateRowDeplResto()
	});
	
	$('#nb_jours_resto').blur(function(){
		updateRowDeplResto()
	});
	
	/**** fin Calculs déplacement ****/
	
	$('#liste_catalogue').css('display', 'none');
	
	$('#view_catalogue').click(function(){
		$('#liste_catalogue').dialog({
			title: 'Catalogue produits',
			width: 1200,
			height: 600
		});
	});
	
	$('#close_catalogue').click(function(){
		$('#liste_catalogue').dialog("close");
	});
	
	$('#calc').css('display', 'none');
	
	$('#view_calc').click(function(){
		$('#calc').dialog({
			title: 'Calculatrice',
			width: 600,
			height: 400
		});
	});
	
	$('#close_calc').click(function(){
		$('#calc').dialog("close");
	});
	
	$('#close_depl').click(function(){
		$('#deplacement').dialog("close");
	});
	
	$('#qte').on('blur', function(){
		updatePrix();
	});
	
	$('#coeff_marge').on('blur', function(){
		updatePrix();
	});
	
	$('#remise').on('blur', function(){
		updatePrix();
	});
	
	$('#ajouter_item').click(function(){
		
		$.post('/ajax/additem', {
			id_devis:$('#id_devis').val(),
			id_item:$('#id_item').val(),
			designation:$('#designation').val(),
			pu:$('#pu').val(),
			qte:$('#qte').val(),
			remise:$('#remise').val(),
			pht:$('#pht').val(),
			marge: $('#coeff_marge').val()
			
		}, function(r){
				$('#liste_items_devis').append(r);
				updateTotal();
				
				
				$('#id_item').val('');
				$('#designation').val('');
				$('#pu').val('');
				$('#qte').val('');
				$('#remise').val('');
				$('#pht').val('');
				$('#coeff_marge').val('');
				
		});
		
		
	});
	

	/* $$$$$$$ Gestion des taches $$$$$$$$$ */
	
	$('#ajouter_tache').click(function(){
		var ok = 1;
		if($('#tache').val() == ''){
			$('#tache').css('border-color', 'red');
			ok = 0;
		}
/*
		if($('#id_user1').val() == 0){
			$('#id_user1').css('border-color', 'red');
			ok = 0;
		}
*/
/*
		if($('#duree').val() == 0){
			$('#duree').css('border-color', 'red');
			ok = 0;
		}
*/
		if(!ok){
			return false;
		}else{
			$.post('/ajax/ajouttache',
			{
				id_tache:$('#id_tache').val(),
				id_projet:$('#id_projet').val(),
				tache:$('#tache').val(),
				id_user1:$('#id_user1').val(),
				id_user2:$('#id_user2').val(),
				id_user3:$('#id_user3').val(),
				duree:$('#duree').val(),
				visible:$('#visible').val()
			},
			function(r){
				if($('#id_tache').val() == ""){
					$('#liste_items_projet').append(r);	
				}else{
					$('#tache_' + $('#id_tache').val()).remove()
					$('#liste_items_projet').append(r);	
				}
				
			});
			
			$('#tache').val('');
			$('#id_user1').val(0);
			$('#id_user2').val(0);
			$('#id_user3').val(0);
			$('#duree').val('');
			$('#id_tache').val('');
		}
	});
	
	$('button[id^="validEtape_"]').click(function(){	
		var id_tache = $(this).attr('id').split('_')[1];
		
		$('#id_tache').val(id_tache);
		$('#validTache').fadeToggle();
		
	});

	$("button[name='btRendTache']").each( function ()
		{
			$(this).click ( function () {
				var idTache = $(this).attr('data-idTache');
				var visible = $(this).attr('data-rendvisible');

				$.post('/ajax/masktache',
					{
						id_tache: idTache,
						visible : visible
					},
					function(r){
						var $row = $("#row_"+idTache);
						$row.attr('data-visible', visible ) ;
						rendTacheInvisible(visible);
                        fctToggleBoutonTachesMasquees(visible);
                    });
            });
		}
	);

	$('button[id^="maskEtape_"]').click(function(){
		var idTache = $(this).attr('id').split('_')[1];
        $("button[name='btRendTache']").each( function ()
        {
			$(this).attr('data-idTache', idTache);
		});

        masckVisibleTache = false;
        var $elem = $("#divVisibleTache");
        $elem.css('position','absolute');
        $elem.css('z-index',100);
        $elem.css('top',$(this).offset().top - 185 );
        $elem.css('left',$(this).offset().left - 50 );
        $elem.removeClass('noDisplay');

        setTimeout( function(){masckVisibleTache = true;}, 500  );

		/*
		var id_tache = $(this).attr('id').split('_')[1];
        var $row = $("#row_"+id_tache);

		var nom = " masquer ";
		if($row.attr('data-visible') === "0")
			nom = " démasquer ";

		if( confirm('Etes-vous sûr(e) de vouloir '+nom+' cette tâche ?') === true)
		{
			$.post('/ajax/masktache',
			{
				id_tache: id_tache,
				visible : (1 - $row.attr('data-visible'))
			},
			function(r){
				var $row = $("#row_"+id_tache);
				$row.attr('data-visible', (1 - $row.attr('data-visible')) ) ;
				rendTacheInvisible();
				alert(r);
			});
		}
		*/
	});	

	$("[name='btAfficheTachesMasquees']").each(function ()
	{
        var dataVisible = $(this).attr('data-mode');
		$(this).click( function()
        {
        	fctToggleBoutonTachesMasquees(dataVisible);

            //$('#txtDropdownMenuTache').html('Les tâches '+ $(this).text() );
			rendTacheInvisible(dataVisible);
        });
	});


rendTacheInvisible(1);

	$('#addNoteTache').click(function(){
		$('#validTache').fadeToggle();

		var $id_tache = $('#id_tache');
		var id_tache = $id_tache.val();
		var $commentaire = $('#commentaire');
		$.post('/ajax/validetape',
	        {
		        id_tache: $id_tache.val(),
		        commentaire: $commentaire.val()
	        }, function(r){
	        var result = r.split('|||');
	        $commentaire.val('');
	        var $etat = $('#etat_' + id_tache);
	        var $date = $('#date_' + id_tache);
	        $etat.empty();
	        $date.empty();
	        $etat.append(result[0]);
	        $date.append(result[1]);
	        $('#note_' + id_tache).append(result[2]);
	    });
	});
	
	/* $$$$$$$$$$ Redaction Devis $$$$$$$$$$$ */
	
	$('#validerRedactionDevis').click(function(){
		$.post('/ajax/saveredaction',
		{
			t:'devis',
			redaction: $('#redactionDevis').val(),
			id_devis: $('#id_devis').val()
		},
		function(){
			
		});
	});
	
	/* $$$$$$$$$$ Redaction FActure $$$$$$$$$$$ */
	
	$('#validerRedactionFacture').click(function(){
		$.post('/ajax/saveredaction',
		{
			t:'facture',
			redaction: $('#redactionFacture').val(),
			id_devis: $('#id_devis').val()
		},
		function(){
			
		});
	});
	
	/* $$$$$$$$$$ Filtres $$$$$$$$$$$ */
	
	$('#projets_clotures').click(function(){
		$.post('/ajax/clotures',{},
		function(r){
			$('#listeProjets').empty();
			$('#listeProjets').append(r);
		});
	});

	$('#filtreRechercheCatalogue').click(function(){
		var ref = $('#reference').val();
		var codeMe = $('#codeMe').val();
		var fournisseur = $('#fournisseur').val();
		var des = $('#produit').val();
		var type = $('#type').val();
		var epaisseur = $('#epaisseur').val();
		var surface_totale = $('#surface_totale').val();

		$('#loader').css('display', 'inline-block');
		
		$.post('/ajax/filtrecatalogue',{
			ref: ref,
			codeMe: codeMe,
			fournisseur: fournisseur,
			designation: des,
			epaisseur: epaisseur,
			surface_totale: surface_totale,
			type: type,
			fromDevis: $('#fromDevis').length
		}, function(r){
			$('#liste_produits').empty();
			$('#liste_produits').append(r);
			
			$('#loader').css('display', 'none');

            $('#liste_produits > tr').click(function(){
                var currentLine = $(this).attr('id');
                localStorage.setItem('currentLine', currentLine);

                $('#liste_produits > tr').css('border', 'none');
                $(this).css('border', '3px solid orange');
            });
		});
	});


	$('#resetFiltreCatalogue').click(function(){

		if ($('#codeMe').length) {
            console.log('Ok');
            $('#codeMe').val('');
            console.log('done');
        }
        if ($('#reference').length) {
            $('#reference').val('');
        }
		$('#fournisseur').val('');
		$('#produit').val('');
		$('#epaisseur').val('');
        if ($('#type').length) {
            $('#type').val('');
        }
		
		$.post('/ajax/filtrecatalogue',{}, 
			function(r){
				$('#liste_produits').empty();
				$('#liste_produits').append(r);
		});
	});

	$('#resetFiltreDevis').click(function(){
		$('#ref').val('');
        $('#num_devis').val('');
		$('#mois').val('');
		$('#annee').val('');
		$('#titre').val('');
		$('#client').val('');

		document.forms['formFiltre'].submit();
	});



/*
	$('#filtreRechercheClotures').click(function(){
			$.post('/ajax/filtreclotures',{
				dossier:$('#dossier').val(),
				num_client:$('#num_client').val(),
				client:$('#client').val(),
				projet:$('#projet').val()
			}, function(){
				location.reload();
			})
		
		});
*/
	
/*
	$('#filtreRecherche').click(function(){
		if($('#type_filtre').val() == 'client'){
			$.post('/ajax/filtreclient', 
			{
				typeFiltre:$('#type_filtre').val(),
				id_client:$('#id_client').val(),
				client:$('#client').val(),
				num_client:$('#num_client').val(),
				adresse:$('#adresse').val()
			}, function(){
	
				location.reload();
			});	
		}else if($('#type_filtre').val() == 'projet'){
			$.post('/ajax/filtreprojet', 
			{
				typeFiltre:$('#type_filtre').val(),
				id_projet:$('#id_projet').val(),
				projet:$('#projet').val(),
				sem:$('#sem').val(),
				num_client:$('#num_client').val(),
				id_client:$('#id_client').val(),
				client:$('#client').val(),
				etapes:$('#etapes').val(),
				user:$('#users').val(),
				dossier:$('#dossier').val()
				
			}, function(){

				location.reload();
			});	
		}else if($('#type_filtre').val() == 'factures'){
			$.post('/ajax/filtrefacture', 
			{
				typeFiltre:$('#type_filtre').val(),
				annee:$('#annee').val(),
				mois:$('#mois').val(),
				valide:$('#valide').val(),
				id_client:$('#id_client').val(),
				client:$('#client').val(),
				id_projet:$('#id_projet').val(),
				projet:$('#projet').val()
			}, function(r){
				$('#listeFactures').empty();
				$('#listeFactures').append(r);
			});	
		}

	});
*/
	
	$('#resetFiltreClient').click(function(){
		$('#client').val('');
		$('#id_client').val('');
		$.post('/ajax/resetfiltreclient', 
		{

		}, function(){
// 			location.reload();
			document.location.href="/clients/"
		})
		
	});
	
	$('#resetFiltreProjet').click(function(){
		$('#client').val('');
		$('#id_client').val('');
		$.post('/ajax/resetfiltreprojet', 
		{

		}, function(){
// 			location.reload();
			document.location.href="/projets/"
		})
		
	});
	
	$('#resetFiltreProjetClotures').click(function(){
		$('#client').val('');
		$('#id_client').val('');
		$.post('/ajax/resetfiltreprojetclotures', 
		{

		}, function(){
			document.location.href="/projets/historique"
		})
		
	});
	
	$('#tabs').tabs();
	
	/****  Calendrier *****/
	
	$('#planTache').css('display', 'none');
	
	$('#validTache').css('display', 'none');
	
	
	$('#openCalendar').click(function(){
        localStorage.removeItem('calendarDayNum');
		window.open('/calendar/','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1200, height=600');
	});
	
	$('#add_event').click(function(){
		var ok = 1;
		if($('#date').val() == 0){
			$('#date').css('border-color', 'red');
			ok = 0;
		}
		if($('#event').val() == 0){
			$('#event').css('border-color', 'red');
			ok = 0;
		}
		if($('#nb_heures').val() == 0){
			$('#nb_heures').css('border-color', 'red');
			ok = 0;
		}
		if($('#id_user').val() == 0){
			$('#id_user').css('border-color', 'red');
			ok = 0;
		}
		if(!ok){
			return false;
		}else{
			$.post('/ajax/addevent',{
				date: $('#date').val(),
				event: $('#event').val(),
				nb_heures: $('#nb_heures').val(),
				id_user: $('#id_user1').val(),
				priority:$('#priority').val(),
			}, function(r){
				$('#' + $('#date').val()).append(r);
				$('#id_user').val(0);
				$('#event').val('');
				$('#date').val(0);
				$('#nb_heures').val('');
			});
		}

	});
	
	$('button[id^="gotoweek_"]').click(function(){
		var w = $(this).attr('id').split('_')[1];
		window.location.href = "/calendar/index/week/" + w;
	});
	
	$('#add_tache_event').click(function(){
		var ok = 1;
		var id_tache = $('#id_tache').val();
		var date = $('#date').val();
		
		if($('#date').val() == 0){
			$('#date').css('border-color', 'red');
			ok = 0;
		}

		if(!ok){
			return false;
		}else{
			$('#alertTache').empty();
			$.post('/ajax/addtachetocal',
			{
				id_user: $('#id_user').val(),
				id_tache: $('#id_tache').val(),
				date: $('#date').val(),
				nb_heures: $('#nb_heures').val(),
				priority: $('#priority').val()
			}, function(r){
				if(r){
					$('#planTache').fadeToggle();
					$('#alertTache').append('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Tache affectée et ajoutée au calendrier.</div>');
					$('#alertTache').fadeToggle();
					
					$('#plan_' + id_tache).empty();
					$('#plan_' + id_tache).append('<span style="color:red;">' + $('#date').val() + '</span>');
				}
			});
			
			$('#id_tache').val('');
			$('#nb_heures').val('');
			$('#priority').val(0);
		}
	});
	
	
	$('#selectUser').val(localStorage.getItem('user_id'));
	
	$('#selectUser').change(function(){

		var id = $(this).val();
		localStorage.setItem('user_id',id);
		
		if(localStorage.getItem('user_id') != 0){
			var id = localStorage.getItem('user_id');
			$('.users').hide();
			$('.user_' + id).show();	
		}
	});
	
	
	/**** Calculatrice ****/
	
	$('.calc').blur(function(){
		var total = parseFloat($('#longueur').val() * $('#largeur').val());
		
		$('#total_calc').val(total.toFixed(3));
	});
	
	$('#qte_calc').blur(function(){
		var total = parseFloat($('#total_calc').val() * $('#qte_calc').val());
		
		$('#total_calc').val(total);
	});
	
	$('#ajouter_calc').click(function(){
		
		var total = $('#total_calc').val();
		
		$('#liste_dimensions').append('<tr><td>'+$('#longueur').val()+'</td><td>'+$('#largeur').val()+'</td><td>'+$('#qte_calc').val()+'</td><td class="add_calc">'+ total +'</td><td>'+ total * 2 +'</td></tr>');
		
		updateTotalCalc();
		
		$('#longueur').val('');
		$('#largeur').val('');
		$('#qte_calc').val(1);
		$('#total_calc').val('');
		
	});

	$("[data-addbloc]").click(function (){
		switch ($(this).attr('data-addbloc'))
		{
			case 'produit':
				fctAddBlocProduit();
				break;
			case 'sousTraitance':
				fctAddBlocSousTraitance();
				break;
			case 'prestation':
				fctAddBlocPrestation();
				break;
			case 'faconnage':
				fctAddBlocFaconnage();
				break;
			case 'adhesif':
				fctAddBlocAdhesif();
				break;
			case 'fraisTechniques':
				fctAddBlocFraisTechniques();
				break;
			case 'forfaitPresation':
				fctAddBlocForfaitPrestation();
				break;
			case 'deplacement':
				fctAddBlocDeplacement();
				break;
			case 'fourniture':
				fctAddBlocFourniture();
				break;
			case 'pose':
				fctAddBlocPose();
				break;

		}
	});

    fctRecalculTout();

});

function fctFillFieldsCollapse()
{
	var fields = "";
    $(".collapse").each(function (ind, elem){ fields += elem.id+";" })

	$("#fieldsCollapse").val(fields);
}

function fctAjaxAddBloc(url, action)
{
    $.post(url,
        {
        },
        function($result){
            var $corps = $("#corps-devis-custom");
            $corps.html($corps.html() + $result);

            var $nbLigne = $("#nbLigne"+action)[0];
            var numLigne = parseInt($nbLigne.value, 10);

            fctBtAjoutSuppLigne(action);

            $nbLigne.value = numLigne + 1;
        }
    )
}

function fctAjoutLigneTableEntete() {
    var $nbLigne = $("#nbLigneTableEntete")[0];
    var numLigne = parseInt($nbLigne.value, 10);

    var ligne = $("#elemCacheTableEntete").clone();
    var $table = $("#tableEntete");
    var html = ligne.html().replace(new RegExp('XXX', 'g'), numLigne);
    $table.append('<tr data-key="ligneRefEntete_'+numLigne+'">'+html+'</tr>');

    $nbLigne.value = numLigne + 1;

}

function fctBtAjoutSuppLigne(action) {

    $("#BtAjoutligne"+action).unbind('click').bind('click',function (){
        var $nbLigne = $("#nbLigne"+action)[0];
        var numLigne = parseInt($nbLigne.value, 10);
        var ligne = $("#elemCache"+action).clone();
        var $table = $("#table"+action);
        var html = ligne.html().replace(new RegExp('XXX', 'g'), numLigne);
        $table.append('<tr data-key="ligneRef'+action+'_'+numLigne+'">'+html+'</tr>');

        $($nbLigne).val(numLigne+1);
    });
    $("#BtSuprimeligne"+action).unbind('click').bind('click',function () {
    	if (confirm("Etes-vous sûr de vouloir supprimer cet élément ?")) {
			$("#panel"+action).remove();
			setTimeout("fctChangeValeurDevis"+action+"();", 500);
    	}
    });
}

function fctChangeValeurRemise()
{
	if ($("#montantTotal").length) {
        var montantTotalInit = $("#montantTotal").html().replace(' euros', '');
        var pourcentageRemise = $("#remise").val();

        var montant = parseFloat((montantTotalInit * pourcentageRemise) / 100).toFixed(2);

        $("#montantTotalRemise").html(parseFloat(montantTotalInit - montant).toFixed(2) + " euros");
    }
}

function fctAddBlocProduit()
{
    fctAjaxAddBloc('/ajax/panelproduits', 'Produit');
}

function fctAddBlocSousTraitance()
{
    fctAjaxAddBloc('/ajax/panelsoustraitance', 'SousTraitance');
}

function fctAddBlocPrestation()
{
    fctAjaxAddBloc('/ajax/panelprestation', 'Prestation');
}

function fctAddBlocFaconnage()
{
    fctAjaxAddBloc('/ajax/panelfaconnage', 'Faconnage');
}

function fctAddBlocAdhesif()
{
    fctAjaxAddBloc('/ajax/paneladhesif', 'Adhesif');
}

function fctAddBlocFraisTechniques()
{
    fctAjaxAddBloc('/ajax/panelfraistechniques', 'FraisTechnique');
}

function fctAddBlocForfaitPrestation()
{
    fctAjaxAddBloc('/ajax/panelforfaitprestation', 'ForfaitPrestation');
}

function fctAddBlocDeplacement()
{
    fctAjaxAddBloc('/ajax/paneldeplacement', 'Deplacement');
}

function fctAddBlocFourniture()
{
    fctAjaxAddBloc('/ajax/panelfourniture', 'Fourniture');
}

function fctAddBlocPose()
{
    fctAjaxAddBloc('/ajax/panelpose', 'Pose');
}

function fctSetFocus()
{
	$("tr").removeClass("focus");
	$(":focus").parent().parent().addClass("focus");
}

function fctRemplitListeClient()
{
    $.post("/ajax/listeclients",
        {
            code: $("#codeClient").val()
        },
        function (result) {
            $("#listeClientsDevis").html(result);

			$("#codeClient").off('input').on('input', function () {
				var option = $("#listeClientsDevis").find("[value='" + $("#codeClient").val() + "']");

				if (option.length > 0) {
					var value = (option.attr("value")).split(' ') ;
					var val = '';
					for(var i=0;i<(value.length-1);i++) {
						val += ' '+value[i];
					}
                    $("#idClient").val(option.attr("data-id"));
                    $("#codeClient").val(option.attr("data-ref"));
					$("#nomClient").val(val.trim());
				}
			});

        }
    );

}

function fctRemplitRefDossier()
{
    $.post("/ajax/listedossier",
        {
            code: $("#refDossier").val()
        },
        function (result) {
            $("#refDossierListe").html(result);

			$("#refDossier").off('input').on('input', function () {
				var option = $("#refDossierListe").find("[value='" + $("#refDossier").val() + "']");

				if (option.length > 0) {
					fctRemplitDossier(option);
				}
			});
        }
    );

}

function fctRemplitDossier(option)
{
    var value = (option.attr("data-titre"));
    $("#intitule").val(value);

    $("#idClient").val(option.attr("data-idClient"));
    $("#codeClient").val(option.attr("data-refClient"));
    $("#nomClient").val(option.attr("data-societeClient").trim());
}

function  fctRemplitListePrivate(nomTable, nomListe, indice, fctFinale)
{
    $.post("/ajax/listeproduits",
        {
            code: $("#"+nomTable+" input[name='code'][data-indice='"+indice+"']").val()
        },
        function (result) {
            $("#"+nomListe+"_"+indice).html(result);
            $("#"+nomTable+" input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#"+nomTable+" input[name='code'][data-indice='"+indice+"']").val();
                        var option = $("#"+nomListe+"_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailproduit",
                                {
                                    id: id
                                },
								function (result2) {
                                    fctFinale(result2)
                                }
                            )
                        }
                    });
                }
            );
        }
    );
}

function fillProduit(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='support'][data-indice='"+indice+"']")).attr('value',  result.designation);
    $($("#"+nomTable+" input[name='format'][data-indice='"+indice+"']")).attr('value', result.format);
    if (parseInt(result.surface_totale, 10) >0) {
        $($("#" + nomTable + " input[name='surface'][data-indice='" + indice + "']")).attr('value', result.surface_totale);
    } else {
        $($("#" + nomTable + " input[name='surface'][data-indice='" + indice + "']")).attr('value', 1);
	}
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value', prix);
    $($("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']")).attr('value', result.coeff_marge);
    fctChangeValeurDevisProduit();
}

function fillAdhesif(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='adhesif'][data-indice='"+indice+"']")).attr('value',result.designation);
    $($("#"+nomTable+" input[name='surface'][data-indice='"+indice+"']")).attr('value',result.surface_totale);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value',prix);
    $($("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']")).attr('value',result.coeff_marge);
    fctChangeValeurDevisAdhesif();
}

function fillDeplacement(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='deplacement'][data-indice='"+indice+"']")).attr('value',result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='tarifUnique'][data-indice='"+indice+"']")).attr('value',prix);
    fctChangeValeurDevisDeplacement();
}

function fillFaconnage(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='faconnage'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value', prix);
    fctChangeValeurDevisFaconnage();
}

function fillForfaitPrestation(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='prestation'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='tarif'][data-indice='"+indice+"']")).attr('value', prix);
    fctChangeValeurDevisForfaitPrestation();
}

function fillFourniture(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='fourniture'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value', prix);
    $($("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']")).attr('value', result.coeff_marge);
    fctChangeValeurDevisFourniture();
}

function fillFraisTechnique(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='fraisTechnique'][data-indice='"+indice+"']")).attr('value', result.designation);
    /*
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']").value = prix;
    $("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']").value = result.coeff_marge;
    */
    fctChangeValeurDevisFraisTechnique();
}

function fillPose(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='pose'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value', prix);
    $($("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']")).attr('value', result.coeff_marge);
    fctChangeValeurDevisPose();
}

function fillPrestation(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $($("#"+nomTable+" input[name='prestation'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='tarif'][data-indice='"+indice+"']")).attr('value', prix);
    fctChangeValeurDevisPrestation();
}

function fillSousTraitance(result, nomTable, indice) {
    $($("#"+nomTable+" input[name='code'][data-indice='"+indice+"']")).attr('value',  result.code_me);
    $$($("#"+nomTable+" input[name='support'][data-indice='"+indice+"']")).attr('value', result.designation);
    var prix = result.prixM2;
    if (!prix || prix === '')
        prix = result.prixML;
    if (!prix || prix === '')
        prix = result.unitaire;
    $($("#"+nomTable+" input[name='h.a_ml'][data-indice='"+indice+"']")).attr('value', prix);
    $($("#"+nomTable+" input[name='coefMarge'][data-indice='"+indice+"']")).attr('value', result.coeff_marge);
    fctChangeValeurDevisSousTraitance();
}

function fctRemplitListeProduit(indice)
{
    var nomTable = "tableProduit";
    var nomListe = "listeProduit";
    fctRemplitListePrivate(
    	nomTable,
		nomListe,
		indice,
		function (result) {
			result = JSON.parse(result);
			fillProduit(result, nomTable, indice);
		}
	)
}

function fctRemplitListeAdhesif(indice)
{
	var nomTable = "tableAdhesif";
	var nomListe = "listeAdhesif";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
		indice,
		function (result) {
			result = JSON.parse(result);
			fillAdhesif(result, nomTable, indice);
		}
	);
}

function fctRemplitListeDeplacement(indice)
{
    var nomTable = "tableDeplacement";
    var nomListe = "listeDeplacement";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillDeplacement(result, nomTable, indice);
        }
    );
}

function fctRemplitListeFaconnage(indice)
{
    var nomTable = "tableFaconnage";
    var nomListe = "listeFaconnage";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillFaconnage(result, nomTable, indice);
        }
    );
}

function fctRemplitListeForfaitPrestation(indice)
{
    var nomTable = "tableForfaitPrestation";
    var nomListe = "listeForfaitPrestation";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillForfaitPrestation(result, nomTable, indice);
        }
    );
}

function fctRemplitListeFourniture(indice)
{
    var nomTable = "tableFourniture";
    var nomListe = "listeFourniture";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillFourniture(result, nomTable, indice);
        }
    );
}

function fctRemplitListeFraisTechnique(indice)
{
    var nomTable = "tableFraisTechnique";
    var nomListe = "listeFraisTechnique";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillFraisTechnique(result, nomTable, indice);
        }
    );
}

function fctRemplitListePose(indice)
{
    var nomTable = "tablePose";
    var nomListe = "listePose";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillPose(result, nomTable, indice);
        }
    );
}

function fctRemplitListePrestation(indice)
{
    var nomTable = "tablePrestation";
    var nomListe = "listePrestation";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
            fillPrestation(result, nomTable, indice);
        }
    );
}

function fctRemplitListeSousTraitance(indice)
{
    var nomTable = "tableSousTraitance";
    var nomListe = "listeSousTraitance";
    fctRemplitListePrivate(
        nomTable,
        nomListe,
        indice,
        function (result) {
            result = JSON.parse(result);
         	fillSousTraitance(result, nomTable, indice);
        }
    );
}

function fctRecalculTout()
{
    fctChangeTableEntete();

    fctBtAjoutSuppLigne("Produit");
    fctBtAjoutSuppLigne("Deplacement");
    fctBtAjoutSuppLigne("Faconnage");
    fctBtAjoutSuppLigne("ForfaitPrestation");
    fctBtAjoutSuppLigne("Fourniture");
    fctBtAjoutSuppLigne("FraisTechnique");
    fctBtAjoutSuppLigne("Pose");
    fctBtAjoutSuppLigne("Prestation");
    fctBtAjoutSuppLigne("SousTraitance");
    fctBtAjoutSuppLigne("Adhesif");

    fctChangeValeurDevisProduit();
    fctChangeValeurDevisDeplacement();
    fctChangeValeurDevisFaconnage();
    fctChangeValeurDevisForfaitPrestation();
    fctChangeValeurDevisFourniture();
    fctChangeValeurDevisFraisTechnique();
    fctChangeValeurDevisPose();
    fctChangeValeurDevisAdhesif();
    fctChangeValeurDevisPrestation();
    fctChangeValeurDevisFaconnage();
    fctChangeValeurDevisSousTraitance();
}

function fctChangeValeurDevisProduit()
{
	var qte 	= $("#tableProduit input[name='qte']");
	var ha 		= $("#tableProduit input[name='h.a_ml']");
	var haTotal = $("#tableProduit input[name='h.a_total']");
	var surface = $("#tableProduit input[name='surface']");

    var code 	   = $("#tableProduit input[name='code']");
    var support = $("#tableProduit input[name='support']");
    var format = $("#tableProduit input[name='format']");
    var coefMarge = $("#tableProduit input[name='coefMarge']");
    var pxvente = $("#tableProduit input[name='pxvente']");

    var jsonLigne  = $("#tableProduit input[name='json_Produit[]']");

	var sommeTotale = 0;
	var sommeVenteTotale = 0;

	for(var i=0;i<qte.length;i++) {
		$(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value).toFixed(2)));
        $(pxvente[i]).attr('value', parseFloat((haTotal[i].value * coefMarge[i].value * surface[i].value).toFixed(2)));
        // $(surface[i]).attr('value', parseFloat((qte[i].value * ha[i].value * haTotal[i].value).toFixed(2)));
		sommeVenteTotale += parseFloat(pxvente[i].value);
		sommeTotale += parseFloat(haTotal[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + format[i].name + '": "' + $(format[i]).val() + '",';
            strJsonLigne += '"' + surface[i].name + '": "' + $(surface[i]).val() + '",';
            strJsonLigne += '"' + coefMarge[i].name + '": "' + $(coefMarge[i]).val() + '",';
            strJsonLigne += '"' + support[i].name + '": "' + $(support[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
	}

	$("#h_a_totauxProduit").html(parseFloat(sommeTotale.toFixed(2))+" euros");
	$("#totalPxVenteProduit").html(parseFloat(sommeVenteTotale).toFixed(2)+" euros");
	$("#margeProduit").html( parseFloat((sommeVenteTotale - sommeTotale).toFixed(2))+" euros");

	fctCalculMontantTotal();
}

function fctChangeValeurDevisSousTraitance()
{
	var qte 	= $("#tableSousTraitance input[name='qte']");
	var ha 		= $("#tableSousTraitance input[name='h.a_ml']");
	var haTotal = $("#tableSousTraitance input[name='h.a_total']");
	var coefMarge = $("#tableSousTraitance input[name='coefMarge']");
	var pxvente = $("#tableSousTraitance input[name='pxvente']");

    var code 	= $("#tableSousTraitance input[name='code']");
    var support	= $("#tableSousTraitance input[name='support']");

    var jsonLigne  = $("#tableSousTraitance input[name='json_SousTraitance[]']");

	var sommeTotale = 0;
	var sommeTotalePxVente = 0;

	for(var i=0;i<qte.length;i++) {
		$(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value).toFixed(2)));
		sommeTotale += parseFloat(haTotal[i].value);
        $(pxvente[i]).attr('value', parseFloat((coefMarge[i].value * haTotal[i].value).toFixed(2)));
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + support[i].name + '": "' + $(support[i]).val() + '",';
            strJsonLigne += '"' + coefMarge[i].name + '": "' + $(coefMarge[i]).val() + '",';
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
	}
	$("#h_a_totauxSousTraitance").html(parseFloat(sommeTotale.toFixed(2))+" euros");
	$("#totalPxVenteSousTraitance").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
	$("#margeSousTraitance").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

	fctCalculMontantTotal();
}

function fctChangeValeurDevisPrestation()
{
	var qte 	   = $("#tablePrestation input[name='qte']");
	var tarif      = $("#tablePrestation input[name='tarif']");
	var pxvente    = $("#tablePrestation input[name='pxvente']");

    var code 	   = $("#tablePrestation input[name='code']");
    var prestation = $("#tablePrestation input[name='prestation']");

    var jsonLigne  = $("#tablePrestation input[name='json_Prestation[]']");

	var sommeTotalePxVente = 0;

	for(var i=0;i<qte.length;i++) {
        $(pxvente[i]).attr('value', parseFloat((tarif[i].value * qte[i].value).toFixed(2)));
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + prestation[i].name + '": "' + $(prestation[i]).val() + '",';
            strJsonLigne += '"' + tarif[i].name + '": "' + $(tarif[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
	}
	$("#h_a_totauxPrestation").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros")	;
	$("#totalPxVentePrestation").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
	$("#margePrestation").html(parseFloat((sommeTotalePxVente - sommeTotalePxVente).toFixed(2))+" euros");

	fctCalculMontantTotal();
}

function fctChangeValeurDevisFaconnage()
{
    var qte 	= $("#tableFaconnage input[name='qte']");
    var ha 		= $("#tableFaconnage input[name='h.a_ml']");
    var haTotal = $("#tableFaconnage input[name='h.a_total']");
    var pxvente = $("#tableFaconnage input[name='pxvente']");

    var code = $("#tableFaconnage input[name='code']");
    var faconnage = $("#tableFaconnage input[name='faconnage']");

    var jsonLigne	= $("#tableFaconnage input[name='json_Faconnage[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        $(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value).toFixed(2)));
        sommeTotale += parseFloat(haTotal[i].value);
        $(pxvente[i]).attr('value', haTotal[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + faconnage[i].name + '": "' + $(faconnage[i]).val() + '",';
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }
    $("#h_a_totauxFaconnage").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVenteFaconnage").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margeFaconnage").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisAdhesif()
{
    var qte 	= $("#tableAdhesif input[name='qte']");
    var surface	= $("#tableAdhesif input[name='surface']");
    var ha 		= $("#tableAdhesif input[name='h.a_ml']");
    var haTotal = $("#tableAdhesif input[name='h.a_total']");
    var coefMarge = $("#tableAdhesif input[name='coefMarge']");
    var pxvente = $("#tableAdhesif input[name='pxvente']");

    var code 	    = $("#tableAdhesif input[name='code']");
    var adhesif		= $("#tableAdhesif input[name='adhesif']");

    var jsonLigne  = $("#tableAdhesif input[name='json_Adhesif[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        $(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value * surface[i].value).toFixed(2)));
        sommeTotale += parseFloat(haTotal[i].value);
        $(pxvente[i]).attr('value', parseFloat((coefMarge[i].value * haTotal[i].value).toFixed(2)));
        sommeTotalePxVente += parseFloat(pxvente[i].value);
		$(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + adhesif[i].name + '": "' + $(adhesif[i]).val() + '",';
            strJsonLigne += '"' + coefMarge[i].name + '": "' + $(coefMarge[i]).val() + '",';
            strJsonLigne += '"' + surface[i].name + '": "' + $(surface[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }
    $("#h_a_totauxAdhesif").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVenteAdhesif").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margeAdhesif").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisFraisTechnique()
{
    var taux 	= $("#tableFraisTechnique input[name='taux']");
    var heures	= $("#tableFraisTechnique input[name='heures']");
    var minutes	= $("#tableFraisTechnique input[name='minutes']");
    var tarifHoraire = $("#tableFraisTechnique input[name='tarifHoraire']");
    var tarifMinute = $("#tableFraisTechnique input[name='tarifMinute']");
    var pxvente = $("#tableFraisTechnique input[name='pxvente']");

    var code 	    	= $("#tableFraisTechnique input[name='code']");
    var fraisTechnique	= $("#tableFraisTechnique input[name='fraisTechnique']");

    var jsonLigne  = $("#tableFraisTechnique input[name='json_FraisTechnique[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<taux.length;i++) {
        $(tarifHoraire[i]).attr('value', parseFloat((taux[i].value * heures[i].value).toFixed(2)));
        $(tarifMinute[i]).attr('value', parseFloat((taux[i].value * minutes[i].value / 60).toFixed(2)));
        $(pxvente[i]).attr('value', (parseFloat(tarifHoraire[i].value) + parseFloat(tarifMinute[i].value)));
        sommeTotale += parseFloat(pxvente[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(taux[i]).attr('value', taux[i].value);

        var strJsonLigne = "";
        if (taux[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + taux[i].name + '": "' + $(taux[i]).val() + '",';
            strJsonLigne += '"' + heures[i].name + '": "' + $(heures[i]).val() + '",';
            strJsonLigne += '"' + minutes[i].name + '": "' + $(minutes[i]).val() + '",';
            strJsonLigne += '"' + tarifHoraire[i].name + '": "' + $(tarifHoraire[i]).val() + '",';
            strJsonLigne += '"' + tarifMinute[i].name + '": "' + $(tarifMinute[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + fraisTechnique[i].name + '": "' + $(fraisTechnique[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }
    $("#h_a_totauxFraisTechnique").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVenteFraisTechnique").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margeFraisTechnique").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisForfaitPrestation()
{
    var qte 	= $("#tableForfaitPrestation input[name='qte']");
    var tarif	= $("#tableForfaitPrestation input[name='tarif']");
    var pxvente = $("#tableForfaitPrestation input[name='pxvente']");

    var code 	   = $("#tableForfaitPrestation input[name='code']");
    var prestation = $("#tableForfaitPrestation input[name='prestation']");

    var jsonLigne  = $("#tableForfaitPrestation input[name='json_ForfaitPrestation[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        $(pxvente[i]).attr('value', parseFloat((parseFloat(qte[i].value) * parseFloat(tarif[i].value)).toFixed(2)));
        sommeTotale += parseFloat(pxvente[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
            strJsonLigne += '"' + prestation[i].name + '": "' + $(prestation[i]).val() + '",';
            strJsonLigne += '"' + tarif[i].name + '": "' + $(tarif[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }
    $("#h_a_totauxForfaitPrestation").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVenteForfaitPrestation").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margeForfaitPrestation").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisDeplacement()
{
    var qte 		= $("#tableDeplacement input[name='qte']");
    var tarifUnique	= $("#tableDeplacement input[name='tarifUnique']");
    var coutJour	= $("#tableDeplacement input[name='coutJour']");
    var nbjours		= $("#tableDeplacement input[name='nbJours']");
    var pxvente	 	= $("#tableDeplacement input[name='pxvente']");
    var code	 	= $("#tableDeplacement input[name='code']");
    var deplacement	= $("#tableDeplacement input[name='deplacement']");

    var jsonLigne	= $("#tableDeplacement input[name='json_Deplacement[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;


    for(var i=0;i<qte.length;i++) {
    	$(coutJour[i]).attr('value', parseFloat((tarifUnique[i].value * qte[i].value).toFixed(2)));
        $(pxvente[i]).attr('value', (parseFloat(nbjours[i].value) * parseFloat(coutJour[i].value)));
        sommeTotale += parseFloat(coutJour[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
        	strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
        	strJsonLigne += '"' + tarifUnique[i].name + '": "' + $(tarifUnique[i]).val() + '",';
        	strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
        	strJsonLigne += '"' + coutJour[i].name + '": "' + $(coutJour[i]).val() + '",';
        	strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '",';
        	strJsonLigne += '"' + nbjours[i].name + '": "' + $(nbjours[i]).val() + '",';
        	strJsonLigne += '"' + deplacement[i].name + '": "' + $(deplacement[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }

	$("#h_a_totauxDeplacement").html(parseFloat(sommeTotale.toFixed(2))+" euros");
	$("#totalPxVenteDeplacement").html(parseFloat((sommeTotalePxVente).toFixed(2))+" euros");
	$("#margeDeplacement").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisFourniture()
{
    var qte 	= $("#tableFourniture input[name='qte']");
    var ha 		= $("#tableFourniture input[name='h.a_ml']");
    var haTotal = $("#tableFourniture input[name='h.a_total']");
    var coefMarge = $("#tableFourniture input[name='coefMarge']");
    var pxvente = $("#tableFourniture input[name='pxvente']");

    var code 	   = $("#tableFourniture input[name='code']");
    var fourniture = $("#tableFourniture input[name='fourniture']");

    var jsonLigne  = $("#tableFourniture input[name='json_Fourniture[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        $(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value).toFixed(2)));
        sommeTotale += parseFloat(haTotal[i].value);
        $(pxvente[i]).attr('value', parseFloat((coefMarge[i].value * haTotal[i].value).toFixed(2)));
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + fourniture[i].name + '": "' + $(fourniture[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + coefMarge[i].name + '": "' + $(coefMarge[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }
    $("#h_a_totauxFourniture").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVenteFourniture").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margeFourniture").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeValeurDevisPose()
{
    var qte 	= $("#tablePose input[name='qte']");
    var ha 		= $("#tablePose input[name='h.a_ml']");
    var haTotal = $("#tablePose input[name='h.a_total']");
    var coefMarge = $("#tablePose input[name='coefMarge']");
    var pxvente = $("#tablePose input[name='pxvente']");

    var code 	   = $("#tablePose input[name='code']");
    var pose = $("#tablePose input[name='pose']");

    var jsonLigne  = $("#tablePose input[name='json_Pose[]']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        $(haTotal[i]).attr('value', parseFloat((qte[i].value * ha[i].value).toFixed(2)));
        sommeTotale += parseFloat(haTotal[i].value);
        $(pxvente[i]).attr('value', parseFloat((coefMarge[i].value * haTotal[i].value).toFixed(2)));
        sommeTotalePxVente += parseFloat(pxvente[i].value);
        $(qte[i]).attr('value', qte[i].value);

        var strJsonLigne = "";
        if (qte[i].value !== "") {
            strJsonLigne += '"' + code[i].name + '": "' + $(code[i]).val() + '",';
            strJsonLigne += '"' + pose[i].name + '": "' + $(pose[i]).val() + '",';
            strJsonLigne += '"' + qte[i].name + '": "' + $(qte[i]).val() + '",';
            strJsonLigne += '"' + ha[i].name + '": "' + $(ha[i]).val() + '",';
            strJsonLigne += '"' + haTotal[i].name + '": "' + $(haTotal[i]).val() + '",';
            strJsonLigne += '"' + coefMarge[i].name + '": "' + $(coefMarge[i]).val() + '",';
            strJsonLigne += '"' + pxvente[i].name + '": "' + $(pxvente[i]).val() + '"';
        }

        $(jsonLigne[i]).attr('value',  '{'+strJsonLigne+'}');
    }

    $("#h_a_totauxPose").html(parseFloat(sommeTotale.toFixed(2))+" euros");
    $("#totalPxVentePose").html(parseFloat(sommeTotalePxVente.toFixed(2))+" euros");
    $("#margePose").html(parseFloat((sommeTotalePxVente - sommeTotale).toFixed(2))+" euros");

    fctCalculMontantTotal();
}

function fctChangeTableEntete()
{
    var largeur 	= $("#tableEntete input[name='largeurTableEntete']");
    var hauteur 	= $("#tableEntete input[name='hauteurTableEntete']");
    var surface 	= $("#tableEntete input[name='surfaceTableEntete']");
    var surfaceTotal= $("#tableEntete input[name='surfTotalTableEntete']");
    var qte 		= $("#tableEntete input[name='qteTableEntete']");

    var coefPub		= $("#tableEntete input[name='coefPubTableEntete']");
    var surfacePub	= $("#tableEntete input[name='surfacePubTableEntete']");
    var perimetre	= $("#tableEntete input[name='perimetreTableEntete']");

    var sommeSurfTotale = 0;
    var sommeSurfPub = 0;
    var sommePerimetre = 0;

    for(var i=0;i<largeur.length;i++) {
        $(surface[i]).attr('value', (largeur[i].value * hauteur[i].value).toFixed(2));
        $(surfaceTotal[i]).attr('value', (surface[i].value * qte[i].value).toFixed(2));

        $(surfacePub[i]).attr('value', (coefPub[i].value * surfaceTotal[i].value).toFixed(2));
        $(perimetre[i]).attr('value', ((parseFloat(largeur[i].value) + parseFloat(hauteur[i].value)) * 2 * qte[i].value).toFixed(2));

        sommeSurfTotale += parseFloat(surfaceTotal[i].value);
        sommeSurfPub += parseFloat(surfacePub[i].value);
        sommePerimetre += parseFloat(perimetre[i].value);
    }
    $("#sommeSurfTotale").html(sommeSurfTotale);
    $("#sommeSurfPub").html(sommeSurfPub);
    $("#sommePerimetre").html(sommePerimetre);

    var jsonLigne  = $("#tableEntete tr");

    var strJsonLigne = " ";
    for(var i=4; i<jsonLigne.length-1;i++) {
    	var jsonInput = $(jsonLigne[i]).find("input");
        strJsonLigne += " { ";
        for(var j=0;j<jsonInput.length;j++) {
            strJsonLigne += '"' + $(jsonInput[j]).attr('name') + '": "' + $(jsonInput[j]).val() + '",';
        }
        strJsonLigne = strJsonLigne.substring(0, strJsonLigne.length - 1) + " },";
    }
    $("#jsonEntete").val('['+strJsonLigne.substring(0, strJsonLigne.length - 1)+']');

    fctCalculMontantTotal();
}

// TODO modifier l'affichage de la pop up pour que cela modifie la bonne ligne dans le devis
// modifier le clic sur les boutons dnt l'id commence par fill_form_ ligne 2229
function fctOuvreCatalogue(indice, action) {

	nomTableGlobal = "table"+action;
	indiceLigneGlobal = indice;

    $('#liste_produits').empty();

    $('#liste_produits > tr').click(function(){
        var currentLine = $(this).attr('id');
        localStorage.setItem('currentLine', currentLine);

        $('#liste_produits > tr').css('border', 'none');
        $(this).css('border', '3px solid orange');
    });

    $('#liste_catalogue').dialog({
        title: 'Catalogue produits',
        width: '95%',
        height: 800
    });


    $("[data-filtre='1']").keypress(function (event) {
		if (event.keyCode === 13) {
			$("#filtreRechercheCatalogue").click();
		}
        $(".ui-dialog").css("z-index", "1050");
	});

};


function fctSupprimeLigneTableEntete() {
	$("#tableEntete .focus").remove();
	setTimeout(fctChangeTableEntete, 500);
}

function fctSupprimeLigneGlobal(indice, action)
{
    if (confirm("Supprimer la ligne ?"))
    {
        $("tr[data-key='ligneRef"+action+"_"+(parseInt(indice, 10))+"']")[0].remove();
        setTimeout(fctRecalculTout, 500);
    }
}

function fctSupprimeLigneEntete(indice)
{
    fctSupprimeLigneGlobal(indice, 'Entete');
}

function fctSupprimeLigneSousTraitance(indice)
{
    fctSupprimeLigneGlobal(indice, 'SousTraitance');
}

function fctSupprimeLigneProduits(indice)
{
    fctSupprimeLigneGlobal(indice, 'Produit');
}

function fctSupprimeLignePrestation(indice)
{
    fctSupprimeLigneGlobal(indice, 'Prestation');
}

function fctSupprimeLignePose(indice)
{
    fctSupprimeLigneGlobal(indice, 'Pose');
}

function fctSupprimeLigneFraisTechnique(indice)
{
    fctSupprimeLigneGlobal(indice, 'FraisTechnique');
}

function fctSupprimeLigneFourniture(indice)
{
    fctSupprimeLigneGlobal(indice, 'Fourniture');
}

function fctSupprimeLigneForfaitPrestation(indice)
{
    fctSupprimeLigneGlobal(indice, 'ForfaitPrestation');
}

function fctSupprimeLigneFaconnage(indice)
{
    fctSupprimeLigneGlobal(indice, 'Faconnage');
}

function fctSupprimeLigneDeplacement(indice)
{
    fctSupprimeLigneGlobal(indice, 'Deplacement');
}

function fctSupprimeLigneAdhesif(indice)
{
    fctSupprimeLigneGlobal(indice, 'Adhesif');
}

function fctCalculMontantTotal() {
	var montant = 0;
	//if ($("#sommePerimetre").length) {
	//	montant += parseFloat($("#sommePerimetre").text());
    //}
    if ($("#totalPxVenteProduit").length) {
		montant += parseFloat($("#totalPxVenteProduit").text());
	}
    if ($("#totalPxVenteDeplacement").length) {
        montant += parseFloat($("#totalPxVenteDeplacement").text());
    }
    if ($("#totalPxVenteFaconnage").length) {
        montant += parseFloat($("#totalPxVenteFaconnage").text());
    }
    if ($("#totalPxVenteForfaitPrestation").length) {
        montant += parseFloat($("#totalPxVenteForfaitPrestation").text());
    }
    if ($("#totalPxVenteFourniture").length) {
        montant += parseFloat($("#totalPxVenteFourniture").text());
    }
    if ($("#totalPxVenteFraisTechnique").length) {
        montant += parseFloat($("#totalPxVenteFraisTechnique").text());
    }
    if ($("#totalPxVentePose").length) {
        montant += parseFloat($("#totalPxVentePose").text());
    }
    if ($("#totalPxVentePrestation").length) {
        montant += parseFloat($("#totalPxVentePrestation").text());
    }
    if ($("#totalPxVenteSousTraitance").length) {
        montant += parseFloat($("#totalPxVenteSousTraitance").text());
    }
    if ($("#totalPxVenteAdhesif").length) {
        montant += parseFloat($("#totalPxVenteAdhesif").text());
    }

	if (!isNaN(montant)) {
        $("#montantTotal").text(parseFloat(montant).toFixed(2) + ' euros');
    }

    fctChangeValeurRemise();
}

$(document).on('blur', 'input[id^="sem_"]', function(){
	var id = $(this).attr('id').split('_')[1];
	$.post('/ajax/updatesem',{
		projet: id,
		sem: $(this).val()
	}, function(){
		
	});
});

$(document).on('click', 'button[id^="getCalendar_"]', function(){
	var id_tache = $(this).attr('id').split('_')[1];
	var id_user = $(this).attr('id').split('_')[2];
	
	$('#id_tache').val(id_tache);
	$('#id_user').val(id_user);
	$('#nb_heures').val($('#nbh_' + id_tache).text());
	
	$('#planTache').fadeToggle();
});

$(document).on('click', 'li[id^="modele_id_"]', function(){
	var id = $(this).attr('id').split('_')[2];
	var active = $(this).attr('id').split('_')[3];
	
	$.post('/ajax/getmodele',{
		id:id
	}, function(r){
		tinyMCE.activeEditor.setContent(r);
	});
});


$(document).on('click','button[id^="fill_form_"]', function(){
	var id = $(this).attr('id').split('_')[2];
	

	$.post("/ajax/detailproduit",
		{
			id: id
		},
		function (result2) {
			result2= JSON.parse(result2);

			if (nomTableGlobal === 'tableProduit') {
                fillProduit(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableSousTraitance') {
                fillSousTraitance(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tablePrestation') {
                fillPrestation(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableFraisTechnique') {
                fillFraisTechnique(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableFourniture') {
                fillFourniture(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableForfaitPrestation') {
                fillForfaitPrestation(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableFaconnage') {
                fillFaconnage(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableDeplacement') {
                fillDeplacement(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tableAdhesif') {
                fillAdhesif(result2 , nomTableGlobal, indiceLigneGlobal);
			}
			if (nomTableGlobal === 'tablePose') {
                fillPose(result2 , nomTableGlobal, indiceLigneGlobal);
			}
		}
	);

	$('#liste_catalogue').dialog("close");

});

$(document).on('click',	'span[id^="delete_item_devis_"]', function(){
	
	if(confirm("Etes-vous sur ?")){
		var id = $(this).attr('id').split('_')[3];
		
		$.post('/ajax/deleteitem', {id_item:id}, function(){
			$('#item_' + id).remove();
			
			updateTotal();
		});
	}

		
});

$(document).on('click', 'button[id^="valid_devis_"]', function(){
	var id_devis = $(this).attr('id').split('_')[2];
	
	$.post('/ajax/validdevis',{
		id_devis:id_devis
	}, function(ok){
		if(ok){
			$('#devis_' + id_devis).remove();
		}
	});
});

	/** Delete file **/
	
$(document).on('click', '#delete_file', function(){
	var url = $(this).next().attr('alt');
	var id_projet = url.split('/')[0];
	
	if(confirm('Etes vous sur ?')){
		$.post('/ajax/delete',{
			f: url
		}, function(r){
			if(r == 'ok'){
				$.post('/ajax/refreshdir',
				{id_projet:id_projet},
				function(r){
					$('#files').empty();
					$('#files').append(r);
				});
			}else{
				alert('Erreur à la suppression du fichier');
			}
		})
	}
});

$(document).on('click', 'button[id^="delete_"]', function(){
	if(confirm('Etes vous sur ?')){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];
		window.location.href = "/" + ctrl + "/delete/id/" + id;
	}
});

$(document).on('click', 'button[id^="fiche_"]', function(){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];
		
		var idPreviousProjet = -1;

		if( $("#idPreviousProject_"+id).length )
			idPreviousProjet = $("#idPreviousProject_"+id).val();

		window.location.href = "/" + ctrl + "/fiche/id/" + id+"/idPrevious/"+idPreviousProjet;

});
	
$(document).on('click', 'button[id^="edit_"]', function(){
		var id = $(this).attr('id').split('_')[2];
		var ctrl = $(this).attr('id').split('_')[1];
		window.location.href = "/" + ctrl + "/editer/id/" + id;
});

$(document).on('click', 'button[id^="edititemprojet_"]', function(){
	
	var id = $(this).attr('id').split('_')[1];

	$.post('/ajax/gettacheinfos',{
		id:id
	}, function(r){
		var infos = r.split('|||');
		$('#id_tache').val(infos[0]);
		$('#tache').val(infos[1]);
		$('#duree').val(infos[3]);
		var users = infos[2];
		var user = users.split('|');
		$('#id_user1').val(user[0]);
		$('#id_user2').val(user[1]);
		$('#id_user3').val(user[2]);
	})	

});

$(document).on('click', 'button[id^="deleteitemprojet_"]', function(){
	if(confirm('Etes-vous sur ?')){
		var id_tache = $(this).attr('id').split('_')[1];
		$.post('/ajax/deletetache',
		{
			id_tache:id_tache
		}, function(){
			$('#tache_' + id_tache).remove();	
		});
	}
});

$(document).on('click', '#disableSelection', function(){

		localStorage.setItem('currentLine', '');
		$('#listeProjets > tr').css('border', 'none');
});

function updatePrix(){
	var pht = eval(($('#pu').val() * $('#qte').val()) * $('#coeff_marge').val());
	
	if(parseFloat($('#remise').val())){
		var remise = eval(pht * ($('#remise').val() / 100));
		$('#pht').val(eval(pht - remise));
	}else{
		$('#pht').val(pht);
	}
}

function updateTotal(){
	var total = 0;
	$('td.totalligne').each(function(){
		total += parseFloat($(this).text());
	});
	
	$('#totalHT').empty();
	$('#totalHT').append(total);
}

function updateForm(id){
	
	$.post('/ajax/getadresse',{
		id_client: id
	}, function(r){
		var infos = r.split('|||');
		
		$('#nomlivraison').val($('#client').val());
		$('#adresselivraison').val(infos[0]);
		$('#codepostal').val(infos[1]);
		$('#ville').val(infos[2]);
		$('#contact_nom').val(infos[3]);
		$('#contact_mail').val(infos[4]);
		$('#contact_tel').val(infos[5]);
		$('#code_client').val(infos[6]);
	});
	
}

function updateTotalCalc(){
	
	var total = 0;
	
	$('td.add_calc').each(function(){
		total += parseFloat($(this).text());
	});
	
	$('#calcTotal').empty();
	$('#calcTotalPeri').empty();
	$('#calcTotal').append(total);
	$('#calcTotalPeri').append(total * 2);
	
}

function updateRowDeplHeure(){
	var total = parseFloat($('#qte_heure').val()) * parseFloat($('#cout_heure').val());
	$('#total_hRoute').empty()
	$('#total_hRoute').append(total)
	
	var gdTotal = total * parseFloat($('#nb_jours_heure').val());
	$('#gd_total_hRoute').empty()
	$('#gd_total_hRoute').append(gdTotal)
	
	updateDeplTotal()
}

function updateRowDeplKm(){
	var total = parseFloat($('#qte_km').val()) * parseFloat($('#cout_km').val());
	$('#total_Km').empty()
	$('#total_Km').append(total)
	
	var gdTotal = total * parseFloat($('#nb_jours_km').val());
	$('#gd_total_Km').empty()
	$('#gd_total_Km').append(gdTotal)
	
	updateDeplTotal()
}

function updateRowDeplResto(){
	var total = parseFloat($('#qte_resto').val()) * parseFloat($('#cout_resto').val());
	$('#total_Resto').empty()
	$('#total_Resto').append(total)
	
	var gdTotal = total * parseFloat($('#nb_jours_resto').val());
	$('#gd_total_Resto').empty()
	$('#gd_total_Resto').append(gdTotal)
	
	updateDeplTotal()
}

function updateDeplTotal(){
	var total = 0;
	
	$('td.depl_total').each(function(){
		if(parseFloat($(this).text()))
		{
			total += parseFloat($(this).text());
		}
	});
	
	$('#gd_total_depl').empty();
	$('#gd_total_depl').append(total);
}

function rendTacheInvisible(dataVisible)
{
    $("[data-visible]").each( function () {
            $(this).addClass('noDisplay');
        }
    );
    var selector = "[data-visible="+dataVisible+"]";
    if(dataVisible == 1)
    {
        selector = "[data-visible!='0'][data-visible!='2']";
    }
    $("[data-visible]"+selector).each( function () {
            $(this).removeClass('noDisplay');
        }
    );
}

function getClassToAdd(elem)
{
	for(var i=0;i<tabColor.length;i++)
	{
		if( elem.hasClass(tabColor[i]) )
			return tabColor[i];
	}
	return "";
}


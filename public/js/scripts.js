var masckPickColor = true;
var masckVisibleTache = true;

var tabColor = ['blue','green','orange', 'red', 'yellow', 'marron', 'gray', 'violet', 'none'];

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

    $("[name='btAfficheTachesMasquees']").each(function()
        {
            if($(this).attr("data-mode")!== dataVisible) {
                $(this).removeClass('active').attr('aria-pressed','false');
            } else {
                $(this).addClass('active').attr('aria-pressed','true');
            }
        }
    );
}

$(document).ready(function(){
    $(document).on('click', fctDisapiredColor);

    var $idPreviousProjet = $("#idPreviousProjet");
    if ($idPreviousProjet.length)
    	localStorage.setItem('currentLine', 'projet_'+$idPreviousProjet.val());

	$("#listeProjets").on('scroll', fctDisapiredColor);

	$(".changeColor").click(function()
		{
			var classToAdd = getClassToAdd( $(this) );
			console.log(classToAdd);
			if( classToAdd == "none" ) {
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
		language_url: '/js/tinymce/langs/fr_FR.js'
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
		var fournisseur = $('#fournisseur').val();
		var des = $('#produit').val();
		var type = $('#type').val();
		var epaisseur = $('#epaisseur').val();
		
		$('#loader').css('display', 'inline-block');
		
		$.post('/ajax/filtrecatalogue',{
			ref: ref,
			fournisseur: fournisseur,
			designation: des,
			epaisseur: epaisseur,
			type: type
		}, function(r){
			$('#liste_produits').empty();
			$('#liste_produits').append(r);
			
			$('#loader').css('display', 'none');
		});
	});
	
	$('#resetFiltreCatalogue').click(function(){
		
		$('#reference').val('');
		$('#fournisseur').val('');
		$('#produit').val('');
		$('#epaisseur').val('');
		$('#type').val('');
		
		$.post('/ajax/filtrecatalogue',{}, 
			function(r){
				$('#liste_produits').empty();
				$('#liste_produits').append(r);
		});
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

});

function fctAjaxAddBloc(url, action)
{
    $.post(url,
        {
        },
        function($result){
            var $corps = $("#corps-devis-custom");
            $corps.html($corps.html() + $result);

            var $nbLigne = $("#nbLigne"+action)[0];
            var numLigne = $nbLigne.value;

            $("#BtAjoutligne"+action).click(function (){
                var ligne = $("#elemCache"+action).clone();
                var $table = $("#table"+action);
                var html = ligne.html().replace(new RegExp('XXX', 'g'), numLigne);
                $table.append('<tr>'+html+'</tr>');
            });
            $("#BtSuprimeligne"+action).click(function () {
                $("#table"+action+" .focus").remove();
                setTimeout("fctChangeValeurDevis"+action+"();", 500);
			});
            $nbLigne.value = numLigne + 1;
        }
    )
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

function fctRemplitListeProduit(indice)
{
	$.post("/ajax/listeproduits",
		{
			code: $("#tableProduit input[name='code']")[indice].value
		},
		function (result) {
			$("#listeProduit_"+indice).html(result);
            $("#tableProduit input[name='code']").each(
            	function () {
                    $(this).on('input', function () {
                        var value = $("#tableProduit input[name='code']")[indice].value;
                        var option = $("#listeProduit_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailproduit",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);
                                    $("#tableProduit input[name='support']")[indice].value = result.designation;
                                    $("#tableProduit input[name='format']")[indice].value = result.format;
                                    $("#tableProduit input[name='coefMarge']")[indice].value = result.coeff_marge;
                                    fctChangeValeurDevisProduit();
                                }
                            )
                        }
                    });
				}
			);
		}
	);

}

function fctRemplitListeAdhesif(indice)
{
    $.post("/ajax/listeadhesif",
        {
            code: $("#tableAdhesif input[name='code']")[indice].value
        },
        function (result) {
            $("#listeAdhesif_"+indice).html(result);
            $("#tableAdhesif input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableAdhesif input[name='code']")[indice].value;
                        var option = $("#listeAdhesif_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailadhesif",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisAdhesif();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeDeplacement(indice)
{
    $.post("/ajax/listedeplacement",
        {
            code: $("#tableDeplacement input[name='code']")[indice].value
        },
        function (result) {
            $("#listeDeplacement_"+indice).html(result);
            $("#tableDeplacement input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableDeplacement input[name='code']")[indice].value;
                        var option = $("#listeDeplacement_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detaildeplacement",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisDeplacement();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeFaconnage(indice)
{
    $.post("/ajax/listefaconnage",
        {
            code: $("#tableFaconnage input[name='code']")[indice].value
        },
        function (result) {
            $("#listeFaconnage_"+indice).html(result);
            $("#tableFaconnage input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableFaconnage input[name='code']")[indice].value;
                        var option = $("#listeFaconnage_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailfaconnage",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisFaconnage();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeForfaitPrestation(indice)
{
    $.post("/ajax/listeforfaitprestation",
        {
            code: $("#tableForfaitPrestation input[name='code']")[indice].value
        },
        function (result) {
            $("#listeForfaitPrestation_"+indice).html(result);
            $("#tableForfaitPrestation input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableForfaitPrestation input[name='code']")[indice].value;
                        var option = $("#listeForfaitPrestation_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailforfaitprestation",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisForfaitPrestation();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeFourniture(indice)
{
    $.post("/ajax/listefourniture",
        {
            code: $("#tableFourniture input[name='code']")[indice].value
        },
        function (result) {
            $("#listeFourniture_"+indice).html(result);
            $("#tableFourniture input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableFourniture input[name='code']")[indice].value;
                        var option = $("#listeFourniture_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailfourniture",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisFourniture();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeFraisTechnique(indice)
{
    $.post("/ajax/listefraistechnique",
        {
            code: $("#tableFraisTechnique input[name='code']")[indice].value
        },
        function (result) {
            $("#listeFraisTechnique_"+indice).html(result);
            $("#tableFraisTechnique input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableFraisTechnique input[name='code']")[indice].value;
                        var option = $("#listeFraisTechnique_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailfraistechnique",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisFraisTechniques();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListePose(indice)
{
    $.post("/ajax/listepose",
        {
            code: $("#tablePose input[name='code']")[indice].value
        },
        function (result) {
            $("#listePose_"+indice).html(result);
            $("#tablePose input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tablePose input[name='code']")[indice].value;
                        var option = $("#listePose_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailpose",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisPose();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListePrestation(indice)
{
    $.post("/ajax/listeprestation",
        {
            code: $("#tablePrestation input[name='code']")[indice].value
        },
        function (result) {
            $("#listePrestation_"+indice).html(result);
            $("#tablePrestation input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tablePrestation input[name='code']")[indice].value;
                        var option = $("#listePrestation_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailprestation",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisPrestation();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctRemplitListeSousTraitance(indice)
{
    $.post("/ajax/listesoustraitance",
        {
            code: $("#tableSousTraitance input[name='code']")[indice].value
        },
        function (result) {
            $("#listeSousTraitance_"+indice).html(result);
            $("#tableSousTraitance input[name='code']").each(
                function () {
                    $(this).on('input', function () {
                        var value = $("#tableSousTraitance input[name='code']")[indice].value;
                        var option = $("#listeSousTraitance_"+indice).find("[value='" + value + "']");

                        if (option.length > 0) {
                            var id = option.data("id");
                            $.post("/ajax/detailsoustraitance",
                                {
                                    id: id
                                },
                                function (result) {
                                    result = JSON.parse(result);

                                    fctChangeValeurDevisSousTraitance();
                                }
                            )
                        }
                    });
                }
            );
        }
    );

}

function fctChangeValeurDevisProduit()
{
	var qte 	= $("#tableProduit input[name='qte']");
	var ha 		= $("#tableProduit input[name='h.a_ml']");
	var haTotal = $("#tableProduit input[name='h.a_total']");

	var sommeTotale = 0;

	for(var i=0;i<qte.length;i++) {
		haTotal[i].value = qte[i].value * ha[i].value;
		sommeTotale += parseFloat(haTotal[i].value);
	}
    $("#tableProduit input[name='totalHA']")[0].value = sommeTotale;
    $("#tableProduit input[name='pxvente']")[0].value = sommeTotale * $("#tableProduit input[name='coefMarge']")[0].value;
	$("#h_a_totauxProduit").html(sommeTotale+" euros");
	$("#totalPxVenteProduit").html($("#tableProduit input[name='pxvente']")[0].value+" euros");
	$("#margeProduit").html( ($("#tableProduit input[name='pxvente']")[0].value - sommeTotale)+" euros");

}

function fctChangeValeurDevisSousTraitance()
{
	var qte 	= $("#tableSousTraitance input[name='qte']");
	var ha 		= $("#tableSousTraitance input[name='h.a_ml']");
	var haTotal = $("#tableSousTraitance input[name='h.a_total']");
	var coefMarge = $("#tableSousTraitance input[name='coefMarge']");
	var pxvente = $("#tableSousTraitance input[name='pxvente']");

	var sommeTotale = 0;
	var sommeTotalePxVente = 0;

	for(var i=0;i<qte.length;i++) {
		haTotal[i].value = qte[i].value * ha[i].value;
		sommeTotale += parseFloat(haTotal[i].value);
        pxvente[i].value = coefMarge[i].value * haTotal[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
	}
	$("#h_a_totauxSousTraitance").html(sommeTotale+" euros");
	$("#totalPxVenteSousTraitance").html(sommeTotalePxVente+" euros");
	$("#margeSousTraitance").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisPrestation()
{
	var qte 	= $("#tablePrestation input[name='qte']");
	var tarif   = $("#tablePrestation input[name='tarif']");
	var pxvente = $("#tablePrestation input[name='pxvente']");

	var sommeTotalePxVente = 0;

	for(var i=0;i<qte.length;i++) {
        pxvente[i].value = tarif[i].value * qte[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
	}
	$("#h_a_totauxPrestation").html(sommeTotalePxVente+" euros")	;
	$("#totalPxVentePrestation").html(sommeTotalePxVente+" euros");
	$("#margePrestation").html((sommeTotalePxVente - sommeTotalePxVente)+" euros");
}

function fctChangeValeurDevisFaconnage()
{
    var qte 	= $("#tableFaconnage input[name='qte']");
    var ha 		= $("#tableFaconnage input[name='h.a_ml']");
    var haTotal = $("#tableFaconnage input[name='h.a_total']");
    var pxvente = $("#tableFaconnage input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        haTotal[i].value = qte[i].value * ha[i].value;
        sommeTotale += parseFloat(haTotal[i].value);
        pxvente[i].value = haTotal[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxFaconnage").html(sommeTotale+" euros");
    $("#totalPxVenteFaconnage").html(sommeTotalePxVente+" euros");
    $("#margeFaconnage").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisAdhesif()
{
    var qte 	= $("#tableAdhesif input[name='qte']");
    var surface	= $("#tableAdhesif input[name='surface']");
    var ha 		= $("#tableAdhesif input[name='h.a_ml']");
    var haTotal = $("#tableAdhesif input[name='h.a_total']");
    var coefMarge = $("#tableAdhesif input[name='coefMarge']");
    var pxvente = $("#tableAdhesif input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        haTotal[i].value = qte[i].value * ha[i].value * surface[i].value;
        sommeTotale += parseFloat(haTotal[i].value);
        pxvente[i].value = coefMarge[i].value * haTotal[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxAdhesif").html(sommeTotale+" euros");
    $("#totalPxVenteAdhesif").html(sommeTotalePxVente+" euros");
    $("#margeAdhesif").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisFraisTechniques()
{
    var taux 	= $("#tableFraisTechnique input[name='taux']");
    var heures	= $("#tableFraisTechnique input[name='heures']");
    var minutes	= $("#tableFraisTechnique input[name='minutes']");
    var tarifHoraire = $("#tableFraisTechnique input[name='tarifHoraire']");
    var tarifMinute = $("#tableFraisTechnique input[name='tarifMinute']");
    var pxvente = $("#tableFraisTechnique input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<taux.length;i++) {
        tarifHoraire[i].value = taux[i].value * heures[i].value;
        tarifMinute[i].value = taux[i].value * minutes[i].value / 60 ;
        pxvente[i].value = parseFloat(tarifHoraire[i].value) + parseFloat(tarifMinute[i].value);
        sommeTotale += parseFloat(pxvente[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxFraisTechnique").html(sommeTotale+" euros");
    $("#totalPxVenteFraisTechnique").html(sommeTotalePxVente+" euros");
    $("#margeFraisTechnique").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisForfaitPrestation()
{
    var qte 	= $("#tableForfaitPrestation input[name='qte']");
    var tarif	= $("#tableForfaitPrestation input[name='tarif']");
    var pxvente = $("#tableForfaitPrestation input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        pxvente[i].value = parseFloat(qte[i].value) * parseFloat(tarif[i].value);
        sommeTotale += parseFloat(pxvente[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxForfaitPrestation").html(sommeTotale+" euros");
    $("#totalPxVenteForfaitPrestation").html(sommeTotalePxVente+" euros");
    $("#margeForfaitPrestation").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisDeplacement()
{
    var qte 		= $("#tableDeplacement input[name='qte']");
    var tarifUnique	= $("#tableDeplacement input[name='tarifUnique']");
    var coutJour	= $("#tableDeplacement input[name='coutJour']");
    var nbjours		= $("#tableDeplacement input[name='nbJours']");
    var pxvente	 	= $("#tableDeplacement input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
    	coutJour[i].value = tarifUnique[i].value * qte[i].value;
        pxvente[i].value = parseFloat(nbjours[i].value) * parseFloat(coutJour[i].value);
        sommeTotale += parseFloat(coutJour[i].value);
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxDeplacement").html(sommeTotale+" euros");
    $("#totalPxVenteDeplacement").html(sommeTotalePxVente+" euros");
    $("#margeDeplacement").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisFourniture()
{
    var qte 	= $("#tableFourniture input[name='qte']");
    var ha 		= $("#tableFourniture input[name='h.a_ml']");
    var haTotal = $("#tableFourniture input[name='h.a_total']");
    var coefMarge = $("#tableFourniture input[name='coefMarge']");
    var pxvente = $("#tableFourniture input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        haTotal[i].value = qte[i].value * ha[i].value;
        sommeTotale += parseFloat(haTotal[i].value);
        pxvente[i].value = coefMarge[i].value * haTotal[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxFourniture").html(sommeTotale+" euros");
    $("#totalPxVenteFourniture").html(sommeTotalePxVente+" euros");
    $("#margeFourniture").html((sommeTotalePxVente - sommeTotale)+" euros");
}

function fctChangeValeurDevisPose()
{
    var qte 	= $("#tablePose input[name='qte']");
    var ha 		= $("#tablePose input[name='h.a_ml']");
    var haTotal = $("#tablePose input[name='h.a_total']");
    var coefMarge = $("#tablePose input[name='coefMarge']");
    var pxvente = $("#tablePose input[name='pxvente']");

    var sommeTotale = 0;
    var sommeTotalePxVente = 0;

    for(var i=0;i<qte.length;i++) {
        haTotal[i].value = qte[i].value * ha[i].value;
        sommeTotale += parseFloat(haTotal[i].value);
        pxvente[i].value = coefMarge[i].value * haTotal[i].value;
        sommeTotalePxVente += parseFloat(pxvente[i].value);
    }
    $("#h_a_totauxPose").html(sommeTotale+" euros");
    $("#totalPxVentePose").html(sommeTotalePxVente+" euros");
    $("#margePose").html((sommeTotalePxVente - sommeTotale)+" euros");
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
	
	$.post('/ajax/produit',{
		id:id
	}, function(r){
		var values = r.split('||');
		
		$('#id_item').val(values[3]);
		$('#designation').val(values[0]);
		$('#pu').val(values[1]);
		$('#coeff_marge').val(values[2]);
		$('#qte').val(1);
		
		updatePrix();
		
		$('#liste_catalogue').dialog("close");
	});
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
		$('#pht').val(eval(pht - remise).toFixed(2));
	}else{
		$('#pht').val(pht.toFixed(2));
	}
}

function updateTotal(){
	var total = 0;
	$('td.totalligne').each(function(){
		total += parseFloat($(this).text());
	});
	
	$('#totalHT').empty();
	$('#totalHT').append(total.toFixed(2));
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


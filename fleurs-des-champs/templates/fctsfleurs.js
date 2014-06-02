function closeAndRefresh() {
  window.opener.focus();
  window.opener.location.reload();
  window.close();
}

function saisieCommentaires (idplante,nomscientif) {
  window.open('commentaires.php?code='+idplante+'&ns='+nomscientif,'','height=450,width=435');
}
  
function controleVide (idplante,mode,nomscientif) {
  if ((document.formulaire.nom.value=='') || (document.formulaire.commentaire.value=='')) {
    alert('Veuillez saisir votre nom et votre commentaire.');
	if (document.formulaire.nom.value=='') {
      document.formulaire.nom.focus();
	} else {
      document.formulaire.commentaire.focus();
	}
  } else {
    document.formulaire.method = "POST";
    document.formulaire.action = "commentaires.php?code="+idplante+"&mode="+mode+"&ns="+nomscientif;
    document.formulaire.submit();
  }
}

function verifSaisieGeoloc () {
	var saisieok = false;
	var emailok = true;
	var vform = document.forms[1];
	latitude = vform.elements["vlatitude"].value;
	longitude = vform.elements["vlongitude"].value;
	if ((latitude == "") || (longitude == "")) {  
		alert("Veuillez indiquer sur la carte votre observation.");
	} else {
		btn = vform.elements["vdate"];
		if (verifDate (btn)) {
			btn = vform.elements["vcertitude"];
			if (verifBtnRadio(btn)) {
				btn = vform.elements["vsurface"];
				if (verifBtnRadio(btn)) {
					btn = vform.elements["vvisible"];
					if (verifBtnRadio(btn)) {
						btn = vform.elements["vnomprenom"];
						if (verifChampTxt(btn)) { 
							btn = vform.elements["vemail"];
							if (verifEmail (btn)) {
								saisieok = true;
							} else {
								emailok = false;
							}
						}
					}
				}
			}
			if (!emailok)  alert ("Votre adresse email n'est pas correcte.");
			else if (!saisieok)  alert ("Veuillez saisir les champs obligatoires.");
		}
	}
	return saisieok;
}


function fct_date_aujourdhui() {
	var vform = document.forms[1];
	var d = new Date();
	var j = d.getDate();
	var m = d.getMonth()+1;
	var y = d.getFullYear();
	vform.elements["vdate"].value = j+"/"+m+"/"+y;
}


// ***** FONCTIONS GENERIQUES ***************************************************************************************************


// verif champs texte non vide
// ******************************************************************************
function verifChampTxt(vChampTxt) {
   var txtsansespaces = vChampTxt.value;
   txtsansespaces = suppEspacesDebut(txtsansespaces);
   txtsansespaces = suppEspacesFin(txtsansespaces);
   var veriftxt = addslashes(addbr(txtsansespaces));
   if (veriftxt=="") {
     // vide champs txt pour supprimer espaces eventuels
     vChampTxt.value = '';
	 return false;
   } else {
	 vChampTxt.value = txtsansespaces;
     return true;
   }
}


function verifBtnRadio(vBtn) {
	ok=false;
	for (var n=0;n<vBtn.length;n++) {
		if (vBtn[n].checked) {
			ok=true;
			n=vBtn.length;
		}
	}
	return ok;
}


// verif adresse email
// ******************************************************************************
function verifEmail (vEmail) {
   email = vEmail.value;
   var reg = /^([a-zA-Z0-9\-_]+[a-zA-Z0-9\.\-_]*@[a-zA-Z0-9\-_]+\.[a-zA-Z\.\-_]{1,}[a-zA-Z\-_]+)$/;
   return reg.test(email);
}


// verifie format date jj/mm/aaaa et date valide
// ------------------------------------------------------------------------------------------------------------------------------
function verifDate (vDate) {
	var ok = 0;
	datesaisie = vDate.value;
	posjour = datesaisie.indexOf("/", 0);
	if (posjour >= 0) {
		jour = datesaisie.substring(0,posjour);
		posmois = datesaisie.indexOf("/", posjour+1);
		if (posmois >= 0) {
			mois = datesaisie.substring(posjour+1,posmois);
			annee = datesaisie.substring(posmois+1);
			ok = verif_format_date(jour, mois, annee);
		}
	} else {
		alert("Veuillez indiquer la date de votre observation.");
	}
	return ok;
}


function verif_format_date(pJour, pMois, pAnnee) {
  var ok=1;
  pJour = parseFloat(pJour);
  pMois = parseFloat(pMois);
  d = new Date();
  pAnneeCourante=d.getFullYear();
  if ( ((isNaN(pJour))||(pJour<1)||(pJour>31)) && (ok==1) ) {
    alert("Le jour de votre date d'observation n'est pas correct."); 
	ok=0;
  }
  if ( ((isNaN(pMois))||(pMois<1)||(pMois>12)) && (ok==1) ) {
    alert("Le mois de votre date d'observation n'est pas correct."); 
	ok=0;
  }
  if ( ((isNaN(pAnnee))||(pAnnee<1970)||(pAnnee>pAnneeCourante)) && (ok==1) ) {
    alert("L'année de votre date d'observation n'est pas correcte."); 
	ok=0;
  }
  if (ok==1) {
    pJour=complete_nombre(pJour);
	pMois=complete_nombre(pMois);
    var d2=new Date(pAnnee,pMois-1,pJour);
    j2=complete_nombre(d2.getDate());
    m2=complete_nombre(d2.getMonth()+1);
    a2=d2.getFullYear();
    if (a2<=100) {a2=1900+a2}
    if ( (pJour!=j2)||(pMois!=m2)||(pAnnee!=a2) ) {
	  datesaisie = pJour+"/"+pMois+"/"+pAnnee;
      alert("La date "+datesaisie+" n'existe pas !");
      ok=0;
    }
  }
  return ok;
}


// supprime les espaces en début de texte
// ******************************************************************************
function suppEspacesDebut(vTxt) {
  if (vTxt!="") {
    if (vTxt.charAt(0)==" ") {
	  while (vTxt.charAt(0) == " ") {
	    vTxt = vTxt.substring(1,vTxt.length);
	 }
    }
  }
  return vTxt;
}


// supprime les espaces en fin de texte
// ******************************************************************************
function suppEspacesFin(vTxt) {
  if (vTxt!="") {
    if (vTxt.charAt(vTxt.length-1)==" ") {
	  while (vTxt.charAt(vTxt.length-1) == " ") {
	    vTxt = vTxt.substring(0,(vTxt.length-1));
	 }
    }
  }
  return vTxt;
}


// remplace les retours à la ligne par des <br>
// ******************************************************************************
function addbr(ch) {
   ch = ch.replace(String.fromCharCode(13),"<br>");
   ch = ch.replace(String.fromCharCode(10),"");
   return ch
}


// ajoute des slashes pour les apostrophes
// ******************************************************************************
function addslashes(ch) {
   ch = ch.replace(/\\/g,"\\\\") 
   ch = ch.replace(/\'/g,"\\'") 
   ch = ch.replace(/\"/g,"\\\"")
   return ch
}

// met un "0" devant un nombre si inférieur à 10
// ******************************************************************************
function complete_nombre(nombre) {
  return ((nombre <= 9) ? "0" : "") + nombre;
}



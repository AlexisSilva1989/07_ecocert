<?php
// (c) EIKONA AG, it.x informationssysteme gmbh, Alle Rechte vorbehalten.		

// Historie ----------------------------------------------------------------------------------------
// 21.05.2014 rsr Erstellung.																			
// Historie ----------------------------------------------------------------------------------------

// !!!!!!!
// HINWEIS:
// Der Cronjob ist f�r 1x t�gliche Ausf�hrung gedacht, mehrmals schadet nicht, ist aber unsinnig.
// !!!!!!!
	
	include("../include/config.php");

	// Einstellungen und Fremdklassen
	
	include("../include/misc.php");
	include("../include/elements.php");
	include("../include/elements_datei.php");

	// Grundlegende Klassen (Datenbank und Anmeldung)
	include("../include/class.Database.php");
	include("../include/class.pageview.php");
	include("../include/class.Session.php");
	include("../include/class.Login.php");
	
	require_once("../admin/include/adminconfig.php");
	require_once("../admin/include/class.LoginInt.php");
	require_once("../admin/include/class.Admin.php");
	require_once("../admin/include/class.adminPreisstaffelung.php");
	
	// Instanziierungen:
	$oSession = new cSession(cstSessionNameExtern);
	$oDatenbank = new cDatabase(cstDBHost, cstDBUser, cstDBPasswd, cstDBName);
	$oAnmeldung = new cLoginInt($oDatenbank, $oSession, "", cstTabUsrExtern, cstTabLinkUsrRollen, cstTabUsrRollen, "de", 1);

	$sMeldung = "\nAktualisierung der Preisstaffelung ...\n";
	
	// Der eigentliche Dienst
	$oPreisstaffelung = new cAdminPreisstaffelung($oDatenbank, $oAnmeldung, $oSession, array(), 0);
	
	// Muss �berhaupt etwas getan werden?
	$bArbeit = false;
	$dGestern = strtotime("-1 day");
	$aBezahlArten = array(
		"PostPaid" => array("art" => 0),
		"PrePaid" => array("art" => 1),
	);
	
	foreach ($aBezahlArten as $aBezahlArt)
	{
		$rsWaehrungen = $oPreisstaffelung->ermitteln_waehrungen_fuer_bezahlart($aBezahlArt["art"]);

		// Gibt es eine W�hrung die aktualisiert werden muss?
		while ($aWaehrung = mysql_fetch_array($rsWaehrungen, MYSQL_ASSOC))
		{
			$aAktuellePreisstaffelung = $oPreisstaffelung->ermitteln_aktuelle_preisstaffelung($aWaehrung["PreisstaffelWaehrung"], $aBezahlArt["art"]);
	
			$dDatumAktuellePreisstaffelung = strtotime($aAktuellePreisstaffelung["PreisstaffelGueltigAbDatum"]);
			
			if ($dDatumAktuellePreisstaffelung == $dGestern)
			{
				$bArbeit = true;
				$sMeldung .= "-> durchzuf�hren f�r \"" . $aWaehrung["PreisstaffelWaehrung"] . "\"\n";
			}
		}
	}

	
	if ($bArbeit == true)
	{
		$oPreisstaffelung->aktualisiere_preisstaffelungen_importeure();
		$sMeldung .= "Aktualisierung durchgef�hrt.\n\n";
	}
	else
		$sMeldung .= "Keine Aktualisierung durchgef�hrt, da nicht notwendig.\n\n";
	
	cy_log($sMeldung, "log", "log/preisstaffelungen_aktualisierungen.log");
?>

<?php
// (c) EIKONA AG, it.x informationssysteme gmbh, Alle Rechte vorbehalten.		

// Historie ----------------------------------------------------------------------------------------
// 29.01.2013 vbr Erstellung.																			
// Historie ----------------------------------------------------------------------------------------
	require_once("admin/include/class.Admin.php");
	require_once("admin/include/class.adminBezahlungTransaktion.php");

	
/**
  * Erstellt die Quartalsausz�ge
 */
	function cron_quartalsabrechnung($aSERVER, $oDatenbank, $oAnmeldung, $oSession)
		{
		$bit_return = false;
		
		// ist der Cronjob f�llig?
		$intDatum = strtotime("- 3 MONTHS");
		
		if (date("Y-m-d", $intDatum) > const_quartalsrechnung_datum)
			{
			// Lesen aller relevanten Adressen
			$rsBenutzer = $oDatenbank->getRecordSet("SELECT * FROM ".cstTabUsrExtern." WHERE UsrPrepaidAktiv = 1");
			
			while($aUser = mysql_fetch_array($rsBenutzer, MYSQL_ASSOC))
				{
				$oBezahlungTransaktion = new cAdminBezahlungTransaktion($oDatenbank, $oAnmeldung, $oSession, array(), 0);
				
				// Durchf�hrung Konsistenzpr�fung
				$aSaldo = $oBezahlungTransaktion->ermittelnSaldo($aUser["UsrId"]);
				
				// Erstellen Monatsauszug
				if ($oBezahlungTransaktion->strFehler == "")
					$oBezahlungTransaktion->createAuszug($aUser["UsrId"], false);
				else
					{
					// Mail generieren
					$oMail = new class_email;
					
					$oMail->empfaenger("support@itx.de");
					
					$oMail->betreff("FEHLER BEI IMO - Konsistenz bei Prepaidkunde");
					
					// Der Einleitungstext wird generiert
					$strMail = "Es gab einen Fehler beim Kunden ".$aUser["UsrId"]."\n".$oBezahlungTransaktion->strFehler."\n".print_r($aUser, true);
					
					// Schlie�lich wird die Mail verschickt
					$oMail->text($strMail);
					$oMail->senden();
					}
				}
			
			// den Zeitpunkt der letzten �nderung f�r diesen Durchlauf in Konfig Datei festhalten.
			if (const_quartalsrechnung_datum != "")
				{
				$str_konfig = lesen_datei("_konfig/konfig_bezahlung.php");
				$str_konfig = str_replace(const_quartalsrechnung_datum, date("Y-m-01"), $str_konfig);
				$bit_ok = schreiben_datei("_konfig/konfig_bezahlung.php", $str_konfig, false);
				}
			}
		else 
			$bit_return = true;

		return $bit_return;
		} // ausfuehren_cron()

?>
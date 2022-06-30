<?php
	// (c) 2002, it.x informationssysteme gmbh, Alle Rechte vorbehalten.
	
	// ------ Historie ------
	// 16.05.01 mwt Erstellung
	// 30.01.02 mwt Anpassung an Fair42
	// 09.04.02 csk Anpassung an Worldcert
	
	// Initialisierung
	include("include/includes.php");
	
	// Seitenvariablen Initialisierung
	$aGET = initGET();
	$aPOST = initPOST();
	$aSERVER = initSERVER();
	
	// Session starten
	$oSession = new cSession(cstSessionNameExtern);
	
	if (isset($aGET))
	{
		while(list($str_name, $str_wert) = each($aGET))
		{
			// Jeder eingepackte Parameter bekommt nun seine eigene Variable
			$$str_name = $str_wert;
		}
	}
	
	// Instanziierung der Seite
	$oDatenbank = new cDatabase(cstDBHost, cstDBUser, cstDBPasswd, cstDBName);
	$oAnmeldung = new cLoginExt($oDatenbank, $oSession, cstLoadPage . "?PageName=" . urlencode($PageName) . "&Language=$Language&Issue=$Issue&NavigationId=$NavigationId&PPID=$PPID", $aSERVER["PHP_SELF"] . "?PageName=" . urlencode($PageName) . "&Language=$Language&Issue=$Issue&NavigationId=$NavigationId&PPID=$PPID", cstTabUsrExtern, cstTabLinkUsrRollen, cstTabUsrRollen, $Language, $Issue);
	
	// Ausgabe auf den Wert aus der Session setzen
	$Issue = $oSession->getValue(cstTabPraefix."Issue");
	if (!$Issue) $Issue = 1;
	
	$oNavigation = new cNavigation($oDatenbank, $oAnmeldung, $PageName, $Language, $Issue, $NavigationId, cstLoadPage, cstPathPicsNav, cstTabNavigation, cstTabSeiten);
	
	// Nun wird die gew�nschte Navigation aufgebaut. Dabei werden auch die Rollen des Benutzers ber�cksichtigt.
	// ggf. werden Navigationspunkte einfach ausgeblendet
	$strNavigation1 = $oNavigation->getNavHorizontal(1, 1, 0, 7);
?>

<html>
	<head>
		<base target="_parent">
		<link rel=stylesheet type="text/css" href="css/cssFormateNavigation1.php">
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<script language="JavaScript" src="<?php echo cstPathScripts; ?>/mauseffekte.js" type="text/javascript">
		</script>
	</head>

	<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#919191">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="71" width="10"><img src="pics/layout/blank.gif" height="71" width="1" border="0"></td>
    <td height="71"><img src="pics/layout/blank.gif" height="71" width="1" border="0"></td>
  </tr>
  <tr>
    <td width="10" height="20"><img src="pics/layout/blank.gif" height="20" width="10" border="0"></td>
    <td valign="top" height="20"><?php echo $strNavigation1; ?></td>
  </tr>
</table>
	</body>
</html>

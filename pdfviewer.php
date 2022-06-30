<?php
// (c) 2002, EIKONA Medien GmbH, it.x informationssysteme gmbh, Alle Rechte vorbehalten.

// Historie ----------------------------------------------------------------------------------------
// 16.12.02 pwr Erstellung																			
// Historie ----------------------------------------------------------------------------------------

define("const_pdf_template_dir", "templates/pdf/");

require_once("include/misc.php");
require_once("include/form-elements.php");

if ($_GET["template"] != "")
	{
	include("include/class_xml_parser.php");
	include("include/class.fpdf.php");
	include("include/class_pdf.php");
	$obj_pdf = new class_pdf();
	$str_template = implode("\n", file(const_pdf_template_dir.$_GET["template"]));
	$obj_pdf->erzeuge_pdf(1, "", $str_template);
	}
else
	{
	$array_templates = getVerzeichnis(const_pdf_template_dir);
	echo "<form action=\"pdfviewer.php\" method=\"get\" target=\"_blank\">";
	echo eComboBoxArray($array_templates, "template", "");
	echo eSubmit("PDF erzeugen");
	echo "</form>";
	}
?>

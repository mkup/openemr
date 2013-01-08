<?php

// Copyright (C) 2012-2016 Mark Kuperman <mkuperman@mi-10.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
//include_once('../interface/globals.php');
require_once ('AccidentClaim.class.php');
require_once ('TherapyNotes.class.php');
require_once ('docTransform.inc.php');
require_once ('populateAccidentForms.inc.php');
require_once ('populatePTN.inc.php');

function gen_accident_form($pid, $encounter, $log, $form) {
    // verify that $form value is valid
    $func = 'gen_' . $form;
    return call_user_func($func, $pid, $encounter, &$log);
}

function gen_NF3($pid, $encounter, $log) {

// Need to get the Form from the Template directory
    $fileInName = $GLOBALS['webserver_root'] . "/templates/transForms/nf3_v1.rtf";
    $inStr = file_get_contents($fileInName);

    $claim = new AccidentClaim($pid, $encounter);
    $claim->nf3Initial();
    $log .= "Generating INITIAL No Fault Form $pid-$encounter for " .
            $claim->patientFirstName() . ' ' .
            $claim->patientMiddleName() . ' ' .
            $claim->patientLastName() . ' on ' .
            date('Y-m-d H:i', time()) . ".\n";

    return populateNF3($claim, $inStr);
}

function gen_NF3FU($pid, $encounter, $log) {

    $fileInName = $GLOBALS['webserver_root'] . "/templates/transForms/nf3_v1.rtf";
    $inStr = file_get_contents($fileInName);

    $claim = new AccidentClaim($pid, $encounter);
    $claim->nf3followup();
    $log .= "Generating FOLLOW-UP No Fault Form $pid-$encounter for " .
            $claim->patientFirstName() . ' ' .
            $claim->patientMiddleName() . ' ' .
            $claim->patientLastName() . ' on ' .
            date('Y-m-d H:i', time()) . ".\n";

    return populateNF3($claim, $inStr);
}

function gen_C40($pid, $encounter, $log) {

    $claim = new AccidentClaim($pid, $encounter);
    $claim->c4Initial();
    $log .= "Generating INITIAL Workers Comp Form $pid-$encounter for " .
            $claim->patientFirstName() . ' ' .
            $claim->patientMiddleName() . ' ' .
            $claim->patientLastName() . ' on ' .
            date('Y-m-d H:i', time()) . ".\n";

    $map = populateC4($claim);

    $fdf = '%FDF-1.2
1 0 obj
<</FDF<</Fields[';
    foreach ($map as $k => $v) {
        $fdf .= "<</T(" . $k . ")/V(" . $v . ")>>";
    }
    $fdf .= ']>>/Type/Catalog>>
endobj
trailer
<</Root 1 0 R>>
%%EOF';

    return $fdf;
}

function gen_C42($pid, $encounter, $log) {

    $claim = new AccidentClaim($pid, $encounter);
    $claim->c4followup();                               //****  to be changed
    $log .= "Generating Follow-Up Workers Comp Form $pid-$encounter for " .
            $claim->patientFirstName() . ' ' .
            $claim->patientMiddleName() . ' ' .
            $claim->patientLastName() . ' on ' .
            date('Y-m-d H:i', time()) . ".\n";

    $map = populateC4($claim);

    $fdf = '%FDF-1.2
1 0 obj
<</FDF<</Fields[';
    foreach ($map as $k => $v) {
        $fdf .= "<</T(" . $k . ")/V(" . $v . ")>>";
    }
    $fdf .= ']>>/Type/Catalog>>
endobj
trailer
<</Root 1 0 R>>
%%EOF';

    // create temp FDF file and pdf output
    $fdfOut = $GLOBALS['temporary_files_dir'] . "/c42-" . date("Y-m-d-Hi", time()) . ".fdf";
    $fileOut = fopen($fdfOut, "w");
    fputs($fileOut, $fdf);
    fclose($fileOut);

    $pdfForm = $GLOBALS['webserver_root'] . "/templates/transForms/c42_v1.pdf";
    $pdfOut = $GLOBALS['temporary_files_dir'] . "/c42-" . date("Y-m-d-Hi", time()) . ".pdf";
    exec('/usr/local/bin/pdftk ' . $pdfForm . ' fill_form ' . $fdfOut . ' output ' . $pdfOut . ' flatten');
    return file_get_contents($pdfOut);
}

function gen_PTN($pid, $encounter, $log) {

    $claim = new TherapyNotes($pid, $encounter);

    $log .= "Generating Physical Therapy Notes $pid-$encounter for " .
            $claim->patientFirstName() . ' ' .
            $claim->patientMiddleName() . ' ' .
            $claim->patientLastName() . ' on ' .
            date('Y-m-d H:i', time()) . ".\n";

    $map = populatePTN($claim);

    $fdf = '%FDF-1.2
1 0 obj
<</FDF<</Fields[';
    foreach ($map as $k => $v) {
        $fdf .= "<</T(" . $k . ")/V(" . $v . ")>>";
    }
    $fdf .= ']>>/Type/Catalog>>
endobj
trailer
<</Root 1 0 R>>
%%EOF';

    // create temp FDF file and pdf output
    $fdfOut = $GLOBALS['temporary_files_dir'] . "/ptn-" . date("Y-m-d-Hi", time()) . ".fdf";
    $fileOut = fopen($fdfOut, "w");
    fputs($fileOut, $fdf);
    fclose($fileOut);

    $pdfForm = $GLOBALS['webserver_root'] . "/templates/transForms/ptn_v1.pdf";
    $pdfOut = $GLOBALS['temporary_files_dir'] . "/ptn-" . date("Y-m-d-Hi", time()) . ".pdf";
    exec('/usr/local/bin/pdftk ' . $pdfForm . ' fill_form ' . $fdfOut . ' output ' . $pdfOut . ' flatten');
    return file_get_contents($pdfOut);
}?>

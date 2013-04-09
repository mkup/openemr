<?php

/*
 * this file's contents are included in both the encounter page as a 'quick summary' of a form, and in the medical records' reports page.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'] . '/api.inc');
/* for generate_display_field() */
require_once($GLOBALS['srcdir'] . '/options.inc.php');
/* The name of the function is significant and must match the folder name */
require_once('_therapy.class.php');

function therapy_report($pid, $encounter, $cols, $id) {
    $manual_layouts = array(
        'diag' =>
        array('field_id' => 'diag',
            'data_type' => '2',
            'fld_length' => '64',
            'max_length' => '64',
            'description' => '',
            'list_id' => ''),
        'total' =>
        array('field_id' => 'total',
            'data_type' => '2',
            'fld_length' => '10',
            'max_length' => '10',
            'description' => '',
            'list_id' => ''),
        'notes' =>
        array('field_id' => 'notes',
            'data_type' => '3',
            'fld_length' => '80',
            'max_length' => '4',
            'description' => '',
            'list_id' => '')
    );

    echo '<table><tr><td>';

    $handler = new _therapy($id);
    echo $handler->matrix('report');

    echo "<tr><td><span class='bold'>";
    echo xl_layout_label('Total: ') . ": ";
    echo '</span><span class=text>' . generate_display_field($manual_layouts['total'], $handler->getTotal().' min.') . '</span></td></tr>';
    echo '</td></tr>';

    echo "<tr><td><span class='bold'>";
    echo xl_layout_label('Diagnosis') . ": ";
    echo '</span><span class=text>' . generate_display_field($manual_layouts['diag'], $handler->getDiag()) . '</span></td></tr>';

    echo "<tr><td><span class='bold'>";
    echo xl_layout_label('Progress Notes') . ": ";
    echo '</span><span class=text>' . generate_display_field($manual_layouts['notes'], $handler->getNotes()) . '</span></td></tr>';
    echo '</table>';
}
?>


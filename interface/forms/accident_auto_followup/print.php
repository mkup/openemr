<?php
/*
 * The page shown when the user requests to print this form. This page automatically prints itsself, and closes its parent browser window.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_form_field, ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_acc_auto_followup';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Auto-accident Follow-up';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'accident_auto_followup';

/* Check the access control lists to ensure permissions to this page */
$thisauth = acl_check('patients', 'med');
if (!$thisauth) {
 die($form_name.': Access Denied.');
}
/* perform a squad check for pages touching patients, if we're in 'athletic team' mode */
if ($GLOBALS['athletic_team']!='false') {
  $tmp = getPatientData($pid, 'squad');
  if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
   $thisauth = 0;
}
/* Use the formFetch function from api.inc to load the saved record */
$xyzzy = formFetch($table_name, $_GET['id']);

/* in order to use the layout engine's draw functions, we need a fake table of layout data. */
$manual_layouts = array( 
 'same_condition' => 
   array( 'field_id' => 'same_condition',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Has Patient ever had same or similar condition?',
          'list_id' => 'NoY' ),
 'same_text' => 
   array( 'field_id' => 'same_text',
          'data_type' => '2',
          'fld_length' => '30',
          'max_length' => '30',
          'description' => 'If YES, when and describe',
          'list_id' => '' ),
 'solely_accident' => 
   array( 'field_id' => 'solely_accident',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Is condition solely a result of this auto accident?',
          'list_id' => 'YN' ),
 'solely_text' => 
   array( 'field_id' => 'solely_text',
          'data_type' => '2',
          'fld_length' => '40',
          'max_length' => '40',
          'description' => 'If NO, explain',
          'list_id' => '' ),
 'due_employment' => 
   array( 'field_id' => 'due_employment',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Is condition due to injury arising out of Patient employment?',
          'list_id' => 'NoY' ),
 'disability' => 
   array( 'field_id' => 'disability',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Will injury result in significant disfigurement or permanent disability?',
          'list_id' => 'yesnona' ),
 'disability_text' => 
   array( 'field_id' => 'disability_text',
          'data_type' => '2',
          'fld_length' => '70',
          'max_length' => '70',
          'description' => 'If YES, describe',
          'list_id' => '' ),
 'disable_from' => 
   array( 'field_id' => 'disable_from',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'Patient was Disable from:',
          'list_id' => '' ),
 'disable_to' => 
   array( 'field_id' => 'disable_to',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'Patient was Disable to:',
          'list_id' => '' ),
 'return_work' => 
   array( 'field_id' => 'return_work',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'If still disable the Patient should be able return to work on:',
          'list_id' => '' ),
 'need_rehab' => 
   array( 'field_id' => 'need_rehab',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Will the patient require rehab and/or Occupational therapy as a result of injury?',
          'list_id' => 'NoY' ),
 'rehab_text' => 
   array( 'field_id' => 'rehab_text',
          'data_type' => '2',
          'fld_length' => '80',
          'max_length' => '80',
          'description' => 'If YES, describe your recommendation',
          'list_id' => '' )
 );

$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

if ($xyzzy['disable_from'] != '') {
    $dateparts = split(' ', $xyzzy['disable_from']);
    $xyzzy['disable_from'] = $dateparts[0];
}
if ($xyzzy['disable_to'] != '') {
    $dateparts = split(' ', $xyzzy['disable_to']);
    $xyzzy['disable_to'] = $dateparts[0];
}
if ($xyzzy['return_work'] != '') {
    $dateparts = split(' ', $xyzzy['return_work']);
    $xyzzy['return_work'] = $dateparts[0];
}

/* define check field functions. used for translating from fields to html viewable strings */

function chkdata_Date(&$record, $var) {
        return htmlspecialchars($record{"$var"},ENT_QUOTES);
}

function chkdata_Txt(&$record, $var) {
        return htmlspecialchars($record{"$var"},ENT_QUOTES);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

<!-- declare this document as being encoded in UTF-8 -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>

<!-- supporting javascript code -->
<!-- for dialog -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/textformat.js"></script>

<!-- Global Stylesheet -->
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css"/>
<!-- Form Specific Stylesheet. -->
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css" type="text/css"/>
<title><?php echo htmlspecialchars('Print '.$form_name); ?></title>

</head>
<body class="body_top">

<div class="print_date"><?php xl('Printed on ','e'); echo date('F d, Y', time()); ?></div>

<form method="post" id="<?php echo $form_folder; ?>" action="">
<div class="title"><?php xl($form_name,'e'); ?></div>

<!-- container for the main body of the form -->
<div id="print_form_container">
<fieldset>

<!-- display the form's manual based fields -->
<table border='0' cellpadding='0' width='100%'>
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="Initial" checked="checked" />Initial Visit Information</td></tr><tr><td><div id="print_Initial" class='section'><table>
<!-- called consumeRows 014--> <!-- Exiting not($fields) and generating 4 empty fields --><td class='emptycell' colspan='1'></td></tr>
</table></div>
</td></tr> <!-- end section Initial -->
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_2' value='1' data-section="Current" checked="checked" />Current Changes</td></tr><tr><td><div id="print_Current" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Same condition prior?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['same_condition'], $xyzzy['same_condition']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, when and describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['same_text'], $xyzzy['same_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Solely result of the Accident?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['solely_accident'], $xyzzy['solely_accident']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If NO, explain','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['solely_text'], $xyzzy['solely_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Condition due employment?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['due_employment'], $xyzzy['due_employment']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will result in PERMANENT disability?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['disability'], $xyzzy['disability']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['disability_text'], $xyzzy['disability_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td>
<span class="fieldlabel"><?php xl('Patient was disable From:','e'); ?>: </span>
</td><td>
   <input type='text' size='10' name='disable_from' id='disable_from' title='Patient was Disable from:'
    value="<?php $result=chkdata_Date($xyzzy,'disable_from'); echo $result; ?>"
    />
</td>
<td>
<span class="fieldlabel"><?php xl('To:','e'); ?>: </span>
</td><td>
   <input type='text' size='10' name='disable_to' id='disable_to' title='Patient was Disable to:'
    value="<?php $result=chkdata_Date($xyzzy,'disable_to'); echo $result; ?>"
    />
</td>
<!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td>
<span class="fieldlabel"><?php xl('Would return to work on','e'); ?>: </span>
</td><td>
   <input type='text' size='10' name='return_work' id='return_work' title='If still disable the Patient should be able return to work on:'
    value="<?php $result=chkdata_Date($xyzzy,'return_work'); echo $result; ?>"
    />
</td>
<!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will require Occupational therapy?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['need_rehab'], $xyzzy['need_rehab']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('    If YES, describe','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['rehab_text'], $xyzzy['rehab_text']); ?></td><!-- called consumeRows 414--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section Current -->
</table>


</fieldset>
</div><!-- end print_form_container -->

</form>
<script type="text/javascript">
window.print();
window.close();
</script>
</body>
</html>


<?php
/*
 * The page shown when the user requests to see this form in a "report view". does not allow editing contents, or saving. has 'print' and 'delete' buttons.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'].'/api.inc');
/* for display_layout_rows(), ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');

/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_acc_auto_initial';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Auto-accident initial visit';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'accident_auto_initial';

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
 'description' => 
   array( 'field_id' => 'description',
          'data_type' => '3',
          'fld_length' => '50',
          'max_length' => '4',
          'description' => 'Describe the accident and injuries',
          'list_id' => '' ),
 'date_accident' => 
   array( 'field_id' => 'date_accident',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'Accident date/Symptom first appear',
          'list_id' => '' ),
 'claim_number' => 
   array( 'field_id' => 'claim_number',
          'data_type' => '2',
          'fld_length' => '15',
          'max_length' => '15',
          'description' => 'Insurance Claim Number',
          'list_id' => '' ),
 'date_symptom' => 
   array( 'field_id' => 'date_symptom',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'When did Symptoms first appear?',
          'list_id' => '' ),
 'date_visit1' => 
   array( 'field_id' => 'date_visit1',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'When did Patient first consult you for this condition',
          'list_id' => '' ),
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

/* since we have no-where to return, abuse returnurl to link to the 'edit' page */
/* FIXME: pass the ID, create blank rows if necissary. */
$returnurl = "../../forms/$form_folder/view.php?mode=noencounter";

/* remove the time-of-day from all date fields */
if ($xyzzy['date_accident'] != '') {
    $dateparts = split(' ', $xyzzy['date_accident']);
    $xyzzy['date_accident'] = $dateparts[0];
}
if ($xyzzy['date_symptom'] != '') {
    $dateparts = split(' ', $xyzzy['date_symptom']);
    $xyzzy['date_symptom'] = $dateparts[0];
}
if ($xyzzy['date_visit1'] != '') {
    $dateparts = split(' ', $xyzzy['date_visit1']);
    $xyzzy['date_visit1'] = $dateparts[0];
}
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
<!-- For jquery, required by edit, print, and delete buttons. -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/textformat.js"></script>

<!-- Global Stylesheet -->
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css"/>
<!-- Form Specific Stylesheet. -->
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css" type="text/css"/>

<script type="text/javascript">

<!-- FIXME: this needs to detect access method, and construct a URL appropriately! -->
function PrintForm() {
    newwin = window.open("<?php echo $rootdir.'/forms/'.$form_folder.'/print.php?id='.$_GET['id']; ?>","print_<?php echo $form_name; ?>");
}

</script>
<title><?php echo htmlspecialchars('Show '.$form_name); ?></title>

</head>
<body class="body_top">

<div id="title">
<span class="title"><?php xl($form_name,'e'); ?></span>
<?php
 if ($thisauth == 'write' || $thisauth == 'addonly')
  { ?>
<a href="<?php echo $returnurl; ?>" onclick="top.restoreSession()">
<span class="back"><?php xl($tmore,'e'); ?></span>
</a>
<?php }; ?>
</div>

<form method="post" id="<?php echo $form_folder; ?>" action="">

<!-- container for the main body of the form -->
<div id="form_container">

<div id="show">

<!-- display the form's manual based fields -->
<table border='0' cellpadding='0' width='100%'>
<tr><td class='sectionlabel'>Accident </td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Accident and Injury Description','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['description'], $xyzzy['description']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Accident Date','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['date_accident'], $xyzzy['date_accident']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Claim number','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['claim_number'], $xyzzy['claim_number']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Symptom first appeared','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['date_symptom'], $xyzzy['date_symptom']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('First visit for this Accident','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['date_visit1'], $xyzzy['date_visit1']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Same condition prior?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['same_condition'], $xyzzy['same_condition']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, when and describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['same_text'], $xyzzy['same_text']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Solely result of the Accident?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['solely_accident'], $xyzzy['solely_accident']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If NO, explain','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['solely_text'], $xyzzy['solely_text']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Condition due employment?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['due_employment'], $xyzzy['due_employment']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will result in PERMANENT disability?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['disability'], $xyzzy['disability']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['disability_text'], $xyzzy['disability_text']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Patient was disable From:','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['disable_from'], $xyzzy['disable_from']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('To:','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['disable_to'], $xyzzy['disable_to']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Would return to work on','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['return_work'], $xyzzy['return_work']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will require Occupational therapy?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['need_rehab'], $xyzzy['need_rehab']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('    If YES, describe','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['rehab_text'], $xyzzy['rehab_text']); ?></td><!-- called consumeRows 414--> <!-- Exiting not($fields)0--></tr>
</table>


</div><!-- end show -->

</div><!-- end form_container -->

<!-- Print button -->
<div id="button_bar" class="button_bar">
<fieldset class="button_bar">
<input type="button" class="print" value="<?php xl('Print','e'); ?>" />
</fieldset>
</div><!-- end button_bar -->

</form>
<script type="text/javascript">
// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    $(".print").click(function() { PrintForm(); });
});
</script>
</body>
</html>


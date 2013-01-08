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
$table_name = 'form_workcomp_followup';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Work-comp follow-up visit';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'workcomp_followup';

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
 'date_accident' => 
   array( 'field_id' => 'date_accident',
          'data_type' => '4',
          'fld_length' => '0',
          'description' => 'Injury date',
          'list_id' => '' ),
 'description' => 
   array( 'field_id' => 'description',
          'data_type' => '3',
          'fld_length' => '50',
          'max_length' => '4',
          'description' => 'Describe the accident and injuries',
          'list_id' => '' ),
 'WCBCase' => 
   array( 'field_id' => 'WCBCase',
          'data_type' => '2',
          'fld_length' => '30',
          'max_length' => '30',
          'description' => 'WCB Case Number (if known)',
          'list_id' => '' ),
 'claim_number' => 
   array( 'field_id' => 'claim_number',
          'data_type' => '2',
          'fld_length' => '30',
          'max_length' => '30',
          'description' => 'Insurance Claim Number',
          'list_id' => '' ),
 'WCBRating' => 
   array( 'field_id' => 'WCBRating',
          'data_type' => '2',
          'fld_length' => '30',
          'max_length' => '30',
          'description' => 'Doctor WCB Rating Code',
          'list_id' => '' ),
 'insurance_W_code' => 
   array( 'field_id' => 'insurance_W_code',
          'data_type' => '2',
          'fld_length' => '30',
          'max_length' => '30',
          'description' => 'Insurance W code number (omit leading W)',
          'list_id' => '' ),
 'visit_tests' => 
   array( 'field_id' => 'visit_tests',
          'data_type' => '3',
          'fld_length' => '40',
          'max_length' => '4',
          'description' => 'Describe any diagnostic test(s) rendered at this visit',
          'list_id' => '' ),
 'exam_changes' => 
   array( 'field_id' => 'exam_changes',
          'data_type' => '3',
          'fld_length' => '40',
          'max_length' => '4',
          'description' => 'List any changes revealed by your most recent examination in the following: area of injury, type/nature of injury, patient subjective complaints or your objective findings',
          'list_id' => '' ),
 'additioanl_body_parts' => 
   array( 'field_id' => 'additioanl_body_parts',
          'data_type' => '3',
          'fld_length' => '40',
          'max_length' => '4',
          'description' => 'List additional body parts affected by this injury, if any',
          'list_id' => '' ),
 'plan_changes' => 
   array( 'field_id' => 'plan_changes',
          'data_type' => '3',
          'fld_length' => '40',
          'max_length' => '4',
          'description' => 'Based on your most recent examination, list changes to the original treatment plan, prescription medications or assistive devices, if any',
          'list_id' => '' ),
 'need_test_refs' => 
   array( 'field_id' => 'need_test_refs',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Does the patient need diagnostic tests or referrals?',
          'list_id' => 'YN' ),
 'test_options' => 
   array( 'field_id' => 'test_options',
          'data_type' => '21',
          'fld_length' => '0',
          'description' => 'If yes, check all that apply',
          'list_id' => 'C4Tests' ),
 'ref_options' => 
   array( 'field_id' => 'ref_options',
          'data_type' => '21',
          'fld_length' => '0',
          'description' => 'If yes, check all that apply',
          'list_id' => 'C4Refs' ),
 'next_appointment' => 
   array( 'field_id' => 'next_appointment',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'When is patient next follow-up visit?',
          'list_id' => 'C4NextVisit' ),
 'visit_treatments' => 
   array( 'field_id' => 'visit_treatments',
          'data_type' => '3',
          'fld_length' => '40',
          'max_length' => '4',
          'description' => 'Describe treatment rendered today',
          'list_id' => '' ),
 'condition_caused' => 
   array( 'field_id' => 'condition_caused',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'In your opinion, was the incident that the patient described the competent medical cause of this injury/illness?',
          'list_id' => 'YN' ),
 'condition_complaints' => 
   array( 'field_id' => 'condition_complaints',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Are the patient complaints consistent with his/her history of the injury/illness?',
          'list_id' => 'YN' ),
 'condition_history' => 
   array( 'field_id' => 'condition_history',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Is the patient history of the injury/illness consistent with your objective findings?',
          'list_id' => 'yesnona' ),
 'percent_impaired' => 
   array( 'field_id' => 'percent_impaired',
          'data_type' => '2',
          'fld_length' => '5',
          'max_length' => '5',
          'description' => 'What is the percentage (0-100%) of temporary impairment?',
          'list_id' => '' ),
 'findings_text' => 
   array( 'field_id' => 'findings_text',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '6',
          'description' => 'Describe findings and relevant diagnostic test results',
          'list_id' => '' ),
 'work_status' => 
   array( 'field_id' => 'work_status',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Is patient working now?',
          'list_id' => 'YN' ),
 'work_restrictions' => 
   array( 'field_id' => 'work_restrictions',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Are there work restrictions?',
          'list_id' => 'NoY' ),
 'work_restrictions_text' => 
   array( 'field_id' => 'work_restrictions_text',
          'data_type' => '3',
          'fld_length' => '50',
          'max_length' => '4',
          'description' => 'If Yes, describe the work restrictions',
          'list_id' => '' ),
 'restrictions_period' => 
   array( 'field_id' => 'restrictions_period',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'How long will the work restrictions apply?',
          'list_id' => 'C4Period' ),
 'work_options' => 
   array( 'field_id' => 'work_options',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Can Patient return to work?(check only one):',
          'list_id' => 'C4Return' ),
 'work_option_text' => 
   array( 'field_id' => 'work_option_text',
          'data_type' => '3',
          'fld_length' => '50',
          'max_length' => '2',
          'description' => 'Please, explain or specify date',
          'list_id' => '' ),
 'limit_options' => 
   array( 'field_id' => 'limit_options',
          'data_type' => '21',
          'fld_length' => '0',
          'description' => 'Return to work limitations',
          'list_id' => 'C4Limits' ),
 'limits_quant_text' => 
   array( 'field_id' => 'limits_quant_text',
          'data_type' => '3',
          'fld_length' => '50',
          'max_length' => '2',
          'description' => 'Describe/quantify the limitations',
          'list_id' => '' ),
 'limits_period' => 
   array( 'field_id' => 'limits_period',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'How long will this limitations apply?',
          'list_id' => 'C4Period' ),
 'return_work_discuss' => 
   array( 'field_id' => 'return_work_discuss',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'With whom will you discuss the patient returning to work and/or limitations?',
          'list_id' => 'C4Discuss' ),
 'benefit_rehab' => 
   array( 'field_id' => 'benefit_rehab',
          'data_type' => '1',
          'fld_length' => '0',
          'description' => 'Would the patient benefit from vocational rehabilitation?',
          'list_id' => 'YN' )
 );

$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

if ($xyzzy['date_accident'] != '') {
    $dateparts = split(' ', $xyzzy['date_accident']);
    $xyzzy['date_accident'] = $dateparts[0];
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
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="WCB" checked="checked" />WCB Info </td></tr><tr><td><div id="print_WCB" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td>
<span class="fieldlabel"><?php xl('Injury Date','e'); ?>: </span>
</td><td>
   <input type='text' size='10' name='date_accident' id='date_accident' title='Injury date'
    value="<?php $result=chkdata_Date($xyzzy,'date_accident'); echo $result; ?>"
    />
</td>
<td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Injury Description','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['description'], $xyzzy['description']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('WCB Case Number','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['WCBCase'], $xyzzy['WCBCase']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Carrier Case number','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['claim_number'], $xyzzy['claim_number']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Doctor WCB Rating Code','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['WCBRating'], $xyzzy['WCBRating']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Carrier W number','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['insurance_W_code'], $xyzzy['insurance_W_code']); ?></td><!-- called consumeRows 424--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section WCB -->
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_2' value='1' data-section="Examination" checked="checked" />D. Examination and Treatment </td></tr><tr><td><div id="print_Examination" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Diagnostic test(s) rendered at this visit','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['visit_tests'], $xyzzy['visit_tests']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Changes revealed by Exam','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['exam_changes'], $xyzzy['exam_changes']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('List additional body parts affected by this injury','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['additioanl_body_parts'], $xyzzy['additioanl_body_parts']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('List changes to the original plan','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['plan_changes'], $xyzzy['plan_changes']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Needs diagnostic tests or referrals?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['need_test_refs'], $xyzzy['need_test_refs']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Tests','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['test_options'], $xyzzy['test_options']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Referrals','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['ref_options'], $xyzzy['ref_options']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('When is the next visit?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['next_appointment'], $xyzzy['next_appointment']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe treatment rendered today','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['visit_treatments'], $xyzzy['visit_treatments']); ?></td><!-- called consumeRows 424--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section Examination -->
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_3' value='1' data-section="Opinion" checked="checked" />E. Doctor Opinion (based on this examination) </td></tr><tr><td><div id="print_Opinion" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Was the incident the competent medical cause of this injury','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['condition_caused'], $xyzzy['condition_caused']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Are the complaints consistent with the history of the injury?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['condition_complaints'], $xyzzy['condition_complaints']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Is the history of the injury consistent with your objective findings?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['condition_history'], $xyzzy['condition_history']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Percent of impairement','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['percent_impaired'], $xyzzy['percent_impaired']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe findings and results','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['findings_text'], $xyzzy['findings_text']); ?></td><!-- called consumeRows 414--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section Opinion -->
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_4' value='1' data-section="Work" checked="checked" />F. Return to Work </td></tr><tr><td><div id="print_Work" class='section'><table>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Is patient working now','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['work_status'], $xyzzy['work_status']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Are there work restrictions','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['work_restrictions'], $xyzzy['work_restrictions']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If Yes, describe restrictions','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['work_restrictions_text'], $xyzzy['work_restrictions_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('How long will the work restrictions apply','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['restrictions_period'], $xyzzy['restrictions_period']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Can Patient return to work?(check only one)','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['work_options'], $xyzzy['work_options']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Explain, or specify date','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['work_option_text'], $xyzzy['work_option_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Lmitations','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['limit_options'], $xyzzy['limit_options']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe the limitations','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['limits_quant_text'], $xyzzy['limits_quant_text']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('How long will this limitations apply','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['limits_period'], $xyzzy['limits_period']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Discussed patient returning to work with','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['return_work_discuss'], $xyzzy['return_work_discuss']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Would the patient benefit from vocational rehabilitation','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['benefit_rehab'], $xyzzy['benefit_rehab']); ?></td><!-- called consumeRows 424--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section Work -->
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


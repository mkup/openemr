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
$table_name = 'form_therapy_notes';

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Physical Therapy Notes';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'therapy_notes';

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
 'diagnosis' => 
   array( 'field_id' => 'diagnosis',
          'data_type' => '21',
          'fld_length' => '0',
          'description' => 'Diagnosis',
          'list_id' => 'TH_diagnosis' ),
 'diag_text' => 
   array( 'field_id' => 'diag_text',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => 'Describe',
          'list_id' => '' ),
 'subjective' => 
   array( 'field_id' => 'subjective',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => 'Describe',
          'list_id' => '' ),
 'objective' => 
   array( 'field_id' => 'objective',
          'data_type' => '21',
          'fld_length' => '0',
          'description' => 'Objective',
          'list_id' => 'TH_objective' ),
 'obj_text' => 
   array( 'field_id' => 'obj_text',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => 'Describe',
          'list_id' => '' ),
 'assessment' => 
   array( 'field_id' => 'assessment',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => 'Assessment',
          'list_id' => '' ),
 'plan' => 
   array( 'field_id' => 'plan',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => 'Plan',
          'list_id' => '' ),
 'provider' => 
   array( 'field_id' => 'provider',
          'data_type' => '10',
          'fld_length' => '0',
          'description' => 'Physical Therapist',
          'list_id' => '' )
 );

$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';


/* define check field functions. used for translating from fields to html viewable strings */

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
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="form" checked="checked" />Notes </td></tr><tr><td><div id="print_form" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Diagnosis','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['diagnosis'], $xyzzy['diagnosis']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['diag_text'], $xyzzy['diag_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Subjective','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['subjective'], $xyzzy['subjective']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Objective','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['objective'], $xyzzy['objective']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['obj_text'], $xyzzy['obj_text']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Assessment','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['assessment'], $xyzzy['assessment']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Plan','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['plan'], $xyzzy['plan']); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Provider','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['provider'], $xyzzy['provider']); ?></td><!-- called consumeRows 214--> <!-- Exiting not($fields) and generating 2 empty fields --><td class='emptycell' colspan='1'></td></tr>
</table></div>
</td></tr> <!-- end section form -->
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


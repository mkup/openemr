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

/* since we have no-where to return, abuse returnurl to link to the 'edit' page */
/* FIXME: pass the ID, create blank rows if necissary. */
$returnurl = "../../forms/$form_folder/view.php?mode=noencounter";


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
<tr><td class='sectionlabel'>Notes </td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Diagnosis','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['diagnosis'], $xyzzy['diagnosis']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['diag_text'], $xyzzy['diag_text']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Subjective','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['subjective'], $xyzzy['subjective']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <!-- called consumeRows 224--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Objective','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['objective'], $xyzzy['objective']); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['obj_text'], $xyzzy['obj_text']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Assessment','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['assessment'], $xyzzy['assessment']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Plan','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_display_field($manual_layouts['plan'], $xyzzy['plan']); ?></td></tr>
<tr><td valign='top'>&nbsp;</td><!-- called consumeRows 014--> <td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Provider','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_display_field($manual_layouts['provider'], $xyzzy['provider']); ?></td><!-- called consumeRows 214--> <!-- Exiting not($fields)2--><td class='emptycell' colspan='1'></td></tr>
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


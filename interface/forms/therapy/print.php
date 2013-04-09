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
require_once('_therapy.class.php');

/** CHANGE THIS name to the name of your form. **/
$form_name = 'Comprehensive Physical Therapy ';

/** CHANGE THIS to match the folder you created for this form. **/
$form_folder = 'therapy';

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

$formid = $_GET['id'];
$handler = new _therapy($formid);

$res = sqlQuery("SELECT * FROM forms WHERE form_id = ? AND formdir = ?", 
                array($formid, $form_folder));
$enc_date = strtotime($res['date']);


$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$manual_layouts = array( 
 'diag' => 
   array( 'field_id' => 'diag',
          'data_type' => '2',
          'fld_length' => '64',
          'max_length' => '64',
          'description' => '',
          'list_id' => '' ),
 'total' =>
        array('field_id' => 'total',
            'data_type' => '2',
            'fld_length' => '10',
            'max_length' => '10',
            'description' => '',
            'list_id' => ''),
 'notes' => 
   array( 'field_id' => 'notes',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => '',
          'list_id' => '' )
    );
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

<div class="print_date"><?php xl('Encounter date ','e'); echo date('F d, Y', $enc_date); ?></div>

<form method="post" id="<?php echo $form_folder; ?>" action="">
<div class="title"><?php xl($form_name,'e'); ?></div>

<!-- container for the main body of the form -->
<div id="print_form_container">
<fieldset>

<!-- display the form's manual based fields -->
<table border='0' cellpadding='0' width='100%' align="left">
    
<tr><td><?php echo $handler->addressHeader(); ?> </td>
    <td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="form" checked="checked" />Comprehensive</td></tr><tr><td><div id="print_form" class='section'><table>
<td class='fieldlabel' colspan='2'><?php echo $handler->matrix('print'); ?></td></tr>
<td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Total','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['total'], $handler->getTotal(). ' min.'); ?></td></tr>
<td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Diagnosis','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['diag'], $handler->getDiag()); ?></td></tr>
<td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Progress Notes','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['notes'], $handler->getNotes()); ?></td></tr>


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


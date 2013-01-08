<?php
/*
 * The page shown when the user requests a new form. allows the user to enter form contents, and save.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_form_field, ?? */
require_once($GLOBALS['srcdir'].'/options.inc.php');
/* note that we cannot include options_listadd.inc here, as it generates code before the <html> tag */

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

if ($thisauth != 'write' && $thisauth != 'addonly')
  die($form_name.': Adding is not authorized.');
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
$submiturl = $GLOBALS['rootdir'].'/forms/'.$form_folder.'/save.php?mode=new&amp;return=encounter';
/* no get logic here */
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

<!-- declare this document as being encoded in UTF-8 -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>

<!-- supporting javascript code -->
<!-- for dialog -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dialog.js"></script>
<!-- For jquery, required by the save and discard buttons. -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/textformat.js"></script>

<!-- Global Stylesheet -->
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css"/>
<!-- Form Specific Stylesheet. -->
<link rel="stylesheet" href="../../forms/<?php echo $form_folder; ?>/style.css" type="text/css"/>

<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot']; ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dynarch_calendar_setup.js"></script>

<script type="text/javascript">
// this line is to assist the calendar text boxes
var mypcc = '<?php echo $GLOBALS['phone_country_code']; ?>';

<!-- a validator for all the fields expected in this form -->
function validate() {
  return true;
}

<!-- a callback for validating field contents. executed at submission time. -->
function submitme() {
 var f = document.forms[0];
 if (validate(f)) {
  top.restoreSession();
  f.submit();
 }
}

</script>



<title><?php echo htmlspecialchars('New '.$form_name); ?></title>

</head>
<body class="body_top">

<div id="title">
<a href="<?php echo $returnurl; ?>" onclick="top.restoreSession()">
<span class="title"><?php xl($form_name,'e'); ?></span>
<span class="back">(<?php xl('Back','e'); ?>)</span>
</a>
</div>

<form method="post" action="<?php echo $submiturl; ?>" id="<?php echo $form_folder; ?>"> 

<!-- Save/Cancel buttons -->
<div id="top_buttons" class="top_buttons">
<fieldset class="top_buttons">
<input type="button" class="save" value="<?php xl('Save','e'); ?>" />
<input type="button" class="dontsave" value="<?php xl('Don\'t Save','e'); ?>" />
</fieldset>
</div><!-- end top_buttons -->

<!-- container for the main body of the form -->
<div id="form_container">
<fieldset>

<!-- display the form's manual based fields -->
<table border='0' cellpadding='0' width='100%'>
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="Initial" checked="checked" />Initial Visit Information</td></tr><tr><td><div id="Initial" class='section'><table>
<!-- called consumeRows 014--> <!-- Exiting not($fields) and generating 4 empty fields --><td class='emptycell' colspan='1'></td></tr>
</table></div>
</td></tr> <!-- end section Initial -->
<tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_2' value='1' data-section="Current" checked="checked" />Current Changes</td></tr><tr><td><div id="Current" class='section'><table>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Same condition prior?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['same_condition'], ''); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, when and describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['same_text'], ''); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Solely result of the Accident?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['solely_accident'], ''); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If NO, explain','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['solely_text'], ''); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Condition due employment?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['due_employment'], ''); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will result in PERMANENT disability?','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['disability'], ''); ?></td><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('If YES, describe','e').':'; ?></td><td class='text data' colspan='1'><?php echo generate_form_field($manual_layouts['disability_text'], ''); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- just calling --><!-- called consumeRows 224--> <!--  generating 4 cells and calling --><td>
<span class="fieldlabel"><?php xl('Patient was disable From:','e'); ?> (yyyy-mm-dd): </span>
</td><td>
   <input type='text' size='10' name='disable_from' id='disable_from' title='Patient was Disable from:'
    value="<?php echo date('Y-m-d', time()); ?>"
    title="<?php xl('yyyy-mm-dd','e'); ?>"
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
   <img src='../../pic/show_calendar.gif' width='24' height='22'
    id='img_disable_from' alt='[?]' style='cursor:pointer'
    title="<?php xl('Click here to choose a date','e'); ?>" />
<script type="text/javascript">
Calendar.setup({inputField:'disable_from', ifFormat:'%Y-%m-%d', button:'img_disable_from'});
</script>
</td>
<td>
<span class="fieldlabel"><?php xl('To:','e'); ?> (yyyy-mm-dd): </span>
</td><td>
   <input type='text' size='10' name='disable_to' id='disable_to' title='Patient was Disable to:'
    value="<?php echo date('Y-m-d', time()); ?>"
    title="<?php xl('yyyy-mm-dd','e'); ?>"
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
   <img src='../../pic/show_calendar.gif' width='24' height='22'
    id='img_disable_to' alt='[?]' style='cursor:pointer'
    title="<?php xl('Click here to choose a date','e'); ?>" />
<script type="text/javascript">
Calendar.setup({inputField:'disable_to', ifFormat:'%Y-%m-%d', button:'img_disable_to'});
</script>
</td>
<!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td>
<span class="fieldlabel"><?php xl('Would return to work on','e'); ?> (yyyy-mm-dd): </span>
</td><td>
   <input type='text' size='10' name='return_work' id='return_work' title='If still disable the Patient should be able return to work on:'
    value="<?php echo date('Y-m-d', time()); ?>"
    title="<?php xl('yyyy-mm-dd','e'); ?>"
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
   <img src='../../pic/show_calendar.gif' width='24' height='22'
    id='img_return_work' alt='[?]' style='cursor:pointer'
    title="<?php xl('Click here to choose a date','e'); ?>" />
<script type="text/javascript">
Calendar.setup({inputField:'return_work', ifFormat:'%Y-%m-%d', button:'img_return_work'});
</script>
</td>
<!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!--  generating 4 cells and calling --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Will require Occupational therapy?','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['need_rehab'], ''); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<!-- called consumeRows 014--> <!-- generating not($fields[$checked+1]) and calling last --><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('    If YES, describe','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['rehab_text'], ''); ?></td><!-- called consumeRows 414--> <!-- Exiting not($fields) and generating 0 empty fields --></tr>
</table></div>
</td></tr> <!-- end section Current -->
</table>

</fieldset>
</div> <!-- end form_container -->

<!-- Save/Cancel buttons -->
<div id="bottom_buttons" class="button_bar">
<fieldset>
<input type="button" class="save" value="<?php xl('Save','e'); ?>" />
<input type="button" class="dontsave" value="<?php xl('Don\'t Save','e'); ?>" />
</fieldset>
</div><!-- end bottom_buttons -->
</form>
<script type="text/javascript">
// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    $(".save").click(function() { top.restoreSession(); document.forms["<?php echo $form_folder; ?>"].submit(); });
    $(".dontsave").click(function() { location.href='<?php echo "$rootdir/patient_file/encounter/$returnurl"; ?>'; });

	$(".sectionlabel input").click( function() {
    	var section = $(this).attr("data-section");
		if ( $(this).attr('checked' ) ) {
			$("#"+section).show();
		} else {
			$("#"+section).hide();
		}
    });

    $(".sectionlabel input").attr( 'checked', 'checked' );
    $(".section").show();
});
</script>
</body>
</html>


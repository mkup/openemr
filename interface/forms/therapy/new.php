<?php
/*
 * The page shown when the user requests a new form. allows the user to enter form contents, and save.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'] . '/api.inc');
/* for generate_form_field, ?? */
require_once($GLOBALS['srcdir'] . '/options.inc.php');
/* note that we cannot include options_listadd.inc here, as it generates code before the <html> tag */
require_once("_therapy.class.php");

/** CHANGE THIS name to the name of your form. * */
$form_name = 'Comprehensive Therapy ';
$table_name = 'form_therapy';
$form_folder = 'therapy';

/* Check the access control lists to ensure permissions to this page */
$thisauth = acl_check('patients', 'med');
if (!$thisauth) {
    die($form_name . ': Access Denied.');
}
/* perform a squad check for pages touching patients, if we're in 'athletic team' mode */
if ($GLOBALS['athletic_team'] != 'false') {
    $tmp = getPatientData($pid, 'squad');
    if ($tmp['squad'] && !acl_check('squads', $tmp['squad']))
        $thisauth = 0;
}

if ($thisauth != 'write' && $thisauth != 'addonly')
    die($form_name . ': Adding is not authorized.');

$formid = formData('id', 'G') + 0;
$manual_layouts = array( 
 'diag' => 
   array( 'field_id' => 'diag',
          'data_type' => '2',
          'fld_length' => '64',
          'max_length' => '64',
          'description' => '',
          'list_id' => '' ),
 'notes' => 
   array( 'field_id' => 'notes',
          'data_type' => '3',
          'fld_length' => '80',
          'max_length' => '4',
          'description' => '',
          'list_id' => '' )
 );
$handler = new _therapy($formid);

$submiturl = $GLOBALS['rootdir'] . '/forms/' . $form_folder . '/save.php?id=' . $formid . '&amp;return=encounter';
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
                x = document.forms["<?php echo $form_folder; ?>"];
                for (i=0; i<x.length; i++) {
                    if (x.elements[i].name.substr(0,4) == 'MTRX' && isNaN(x.elements[i].value)) {
                        m = "Please, enter numbers only ";
                        alert(m)
                        x.elements[i].focus();
                        return false;
                    }
                }
                return true;
            }

        </script>

        <title><?php echo htmlspecialchars('New ' . $form_name); ?></title>

    </head>
    <body class="body_top">

        <div id="title">
            <a href="<?php echo $returnurl; ?>" onclick="top.restoreSession()">
                <span class="title"><?php xl($form_name, 'e'); ?></span>
                <span class="back">(<?php xl('Back', 'e'); ?>)</span>
            </a>
        </div>

        <form method="post" action="<?php echo $submiturl; ?>" id="<?php echo $form_folder; ?>"> 

            <!-- Save/Cancel buttons -->
            <div id="top_buttons" class="top_buttons">
                <fieldset class="top_buttons">
                    <input type="button" class="save" name="bn_save" value="<?php xl('Save', 'e'); ?>" />
                    <input type="button" class="dontsave" name="bn_cancel" value="<?php xl('Don\'t Save', 'e'); ?>" />
                </fieldset>
            </div><!-- end top_buttons -->

            <!-- container for the main body of the form -->
            <div id="form_container">
                <fieldset>

                    <!-- display the form's manual based fields -->
                    <table border='0' cellpadding='0' width='100%'>
                    <!-- <tr><td class='sectionlabel'><input type='checkbox' id='form_cb_m_1' value='1' data-section="form" checked="checked" />Comprehensive</td></tr><tr><td><div id="form" class='section'>
                    <table>
                    called consumeRows 012--> <!-- generating not($fields[$checked+1]) and calling last -->
<tr><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Diagnosis','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['diag'], $handler->getDiag()); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>
<tr><td class='fieldlabel' colspan='1'><?php echo xl_layout_label('Progress Notes','e').':'; ?></td><td class='text data' colspan='3'><?php echo generate_form_field($manual_layouts['notes'], $handler->getNotes()); ?></td><!--  generating empties --><td class='emptycell' colspan='1'></td></tr>

<tr><td colspan="4">
<?php echo $handler->matrix('input'); ?>

                            </td></tr> <!-- end section form -->
                    </table>

                </fieldset>
            </div> <!-- end form_container -->

            <!-- Save/Cancel buttons -->
            <div id="bottom_buttons" class="button_bar">
                <fieldset>
                    <input type="button" class="save" name="bn_save" value="<?php xl('Save', 'e'); ?>" />
                    <input type="button" class="dontsave" name="bn_cancel" value="<?php xl('Don\'t Save', 'e'); ?>" />
                </fieldset>
            </div><!-- end bottom_buttons -->
        </form>
        <script type="text/javascript">
            // jQuery stuff to make the page a little easier to use

            $(document).ready(function(){
                $(".save").click(function() { top.restoreSession(); if (validate()) document.forms["<?php echo $form_folder; ?>"].submit(); });
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


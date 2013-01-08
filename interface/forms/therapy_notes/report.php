<?php
/*
 * this file's contents are included in both the encounter page as a 'quick summary' of a form, and in the medical records' reports page.
 */

/* for $GLOBALS[], ?? */
require_once('../../globals.php');
/* for acl_check(), ?? */
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_display_field() */
require_once($GLOBALS['srcdir'].'/options.inc.php');
/* The name of the function is significant and must match the folder name */
function therapy_notes_report( $pid, $encounter, $cols, $id) {
    $count = 0;
/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_therapy_notes';


/* an array of all of the fields' names and their types. */
$field_names = array('diagnosis' => 'checkbox_list','diag_text' => 'textarea','subjective' => 'textarea','objective' => 'checkbox_list','obj_text' => 'textarea','assessment' => 'textarea','plan' => 'textarea','provider' => 'provider');/* in order to use the layout engine's draw functions, we need a fake table of layout data. */
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
/* an array of the lists the fields may draw on. */
$lists = array();
    $data = formFetch($table_name, $id);
    if ($data) {

        echo '<table><tr>';

        foreach($data as $key => $value) {

            if ($key == 'id' || $key == 'pid' || $key == 'user' ||
                $key == 'groupname' || $key == 'authorized' ||
                $key == 'activity' || $key == 'date' || 
                $value == '' || $value == '0000-00-00 00:00:00' ||
                $value == 'n')
            {
                /* skip built-in fields and "blank data". */
	        continue;
            }

            /* display 'yes' instead of 'on'. */
            if ($value == 'on') {
                $value = 'yes';
            }

            /* remove the time-of-day from the 'date' fields. */
            if ($field_names[$key] == 'date')
            if ($value != '') {
              $dateparts = split(' ', $value);
              $value = $dateparts[0];
            }

	    echo "<td><span class='bold'>";
            

            if ($key == 'diagnosis' ) 
            { 
                echo xl_layout_label('Diagnosis').":";
            }

            if ($key == 'diag_text' ) 
            { 
                echo xl_layout_label('Describe').":";
            }

            if ($key == 'subjective' ) 
            { 
                echo xl_layout_label('Subjective').":";
            }

            if ($key == 'objective' ) 
            { 
                echo xl_layout_label('Objective').":";
            }

            if ($key == 'obj_text' ) 
            { 
                echo xl_layout_label('Describe').":";
            }

            if ($key == 'assessment' ) 
            { 
                echo xl_layout_label('Assessment').":";
            }

            if ($key == 'plan' ) 
            { 
                echo xl_layout_label('Plan').":";
            }

            if ($key == 'provider' ) 
            { 
                echo xl_layout_label('Provider').":";
            }

                echo '</span><span class=text>'.generate_display_field( $manual_layouts[$key], $value ).'</span></td>';

            $count++;
            if ($count == $cols) {
                $count = 0;
                echo '</tr><tr>' . PHP_EOL;
            }
        }
    }
    echo '</tr></table><hr>';
}
?>


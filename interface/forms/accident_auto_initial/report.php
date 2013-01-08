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
function accident_auto_initial_report( $pid, $encounter, $cols, $id) {
    $count = 0;
/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_acc_auto_initial';


/* an array of all of the fields' names and their types. */
$field_names = array('description' => 'textarea','date_accident' => 'date','claim_number' => 'textfield','date_symptom' => 'date','date_visit1' => 'date','same_condition' => 'dropdown_list','same_text' => 'textfield','solely_accident' => 'dropdown_list','solely_text' => 'textfield','due_employment' => 'dropdown_list','disability' => 'dropdown_list','disability_text' => 'textfield','disable_from' => 'date','disable_to' => 'date','return_work' => 'date','need_rehab' => 'dropdown_list','rehab_text' => 'textfield');/* in order to use the layout engine's draw functions, we need a fake table of layout data. */
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
            

            if ($key == 'description' ) 
            { 
                echo xl_layout_label('Accident and Injury Description').":";
            }

            if ($key == 'date_accident' ) 
            { 
                echo xl_layout_label('Accident Date').":";
            }

            if ($key == 'claim_number' ) 
            { 
                echo xl_layout_label('Claim number').":";
            }

            if ($key == 'date_symptom' ) 
            { 
                echo xl_layout_label('Symptom first appeared').":";
            }

            if ($key == 'date_visit1' ) 
            { 
                echo xl_layout_label('First visit for this Accident').":";
            }

            if ($key == 'same_condition' ) 
            { 
                echo xl_layout_label('Same condition prior?').":";
            }

            if ($key == 'same_text' ) 
            { 
                echo xl_layout_label('If YES, when and describe').":";
            }

            if ($key == 'solely_accident' ) 
            { 
                echo xl_layout_label('Solely result of the Accident?').":";
            }

            if ($key == 'solely_text' ) 
            { 
                echo xl_layout_label('If NO, explain').":";
            }

            if ($key == 'due_employment' ) 
            { 
                echo xl_layout_label('Condition due employment?').":";
            }

            if ($key == 'disability' ) 
            { 
                echo xl_layout_label('Will result in PERMANENT disability?').":";
            }

            if ($key == 'disability_text' ) 
            { 
                echo xl_layout_label('If YES, describe').":";
            }

            if ($key == 'disable_from' ) 
            { 
                echo xl_layout_label('Patient was disable From:').":";
            }

            if ($key == 'disable_to' ) 
            { 
                echo xl_layout_label('To:').":";
            }

            if ($key == 'return_work' ) 
            { 
                echo xl_layout_label('Would return to work on').":";
            }

            if ($key == 'need_rehab' ) 
            { 
                echo xl_layout_label('Will require Occupational therapy?').":";
            }

            if ($key == 'rehab_text' ) 
            { 
                echo xl_layout_label('    If YES, describe').":";
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


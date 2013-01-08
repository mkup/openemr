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
function workcomp_followup_report( $pid, $encounter, $cols, $id) {
    $count = 0;
/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_workcomp_followup';


/* an array of all of the fields' names and their types. */
$field_names = array('date_accident' => 'date','description' => 'textarea','WCBCase' => 'textfield','claim_number' => 'textfield','WCBRating' => 'textfield','insurance_W_code' => 'textfield','visit_tests' => 'textarea','exam_changes' => 'textarea','additioanl_body_parts' => 'textarea','plan_changes' => 'textarea','need_test_refs' => 'dropdown_list','test_options' => 'checkbox_list','ref_options' => 'checkbox_list','next_appointment' => 'dropdown_list','visit_treatments' => 'textarea','condition_caused' => 'dropdown_list','condition_complaints' => 'dropdown_list','condition_history' => 'dropdown_list','percent_impaired' => 'textfield','findings_text' => 'textarea','work_status' => 'dropdown_list','work_restrictions' => 'dropdown_list','work_restrictions_text' => 'textarea','restrictions_period' => 'dropdown_list','work_options' => 'dropdown_list','work_option_text' => 'textarea','limit_options' => 'checkbox_list','limits_quant_text' => 'textarea','limits_period' => 'dropdown_list','return_work_discuss' => 'dropdown_list','benefit_rehab' => 'dropdown_list');/* in order to use the layout engine's draw functions, we need a fake table of layout data. */
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
            

            if ($key == 'date_accident' ) 
            { 
                echo xl_layout_label('Injury Date').":";
            }

            if ($key == 'description' ) 
            { 
                echo xl_layout_label('Injury Description').":";
            }

            if ($key == 'WCBCase' ) 
            { 
                echo xl_layout_label('WCB Case Number').":";
            }

            if ($key == 'claim_number' ) 
            { 
                echo xl_layout_label('Carrier Case number').":";
            }

            if ($key == 'WCBRating' ) 
            { 
                echo xl_layout_label('Doctor WCB Rating Code').":";
            }

            if ($key == 'insurance_W_code' ) 
            { 
                echo xl_layout_label('Carrier W number').":";
            }

            if ($key == 'visit_tests' ) 
            { 
                echo xl_layout_label('Diagnostic test(s) rendered at this visit').":";
            }

            if ($key == 'exam_changes' ) 
            { 
                echo xl_layout_label('Changes revealed by Exam').":";
            }

            if ($key == 'additioanl_body_parts' ) 
            { 
                echo xl_layout_label('List additional body parts affected by this injury').":";
            }

            if ($key == 'plan_changes' ) 
            { 
                echo xl_layout_label('List changes to the original plan').":";
            }

            if ($key == 'need_test_refs' ) 
            { 
                echo xl_layout_label('Needs diagnostic tests or referrals?').":";
            }

            if ($key == 'test_options' ) 
            { 
                echo xl_layout_label('Tests').":";
            }

            if ($key == 'ref_options' ) 
            { 
                echo xl_layout_label('Referrals').":";
            }

            if ($key == 'next_appointment' ) 
            { 
                echo xl_layout_label('When is the next visit?').":";
            }

            if ($key == 'visit_treatments' ) 
            { 
                echo xl_layout_label('Describe treatment rendered today').":";
            }

            if ($key == 'condition_caused' ) 
            { 
                echo xl_layout_label('Was the incident the competent medical cause of this injury').":";
            }

            if ($key == 'condition_complaints' ) 
            { 
                echo xl_layout_label('Are the complaints consistent with the history of the injury?').":";
            }

            if ($key == 'condition_history' ) 
            { 
                echo xl_layout_label('Is the history of the injury consistent with your objective findings?').":";
            }

            if ($key == 'percent_impaired' ) 
            { 
                echo xl_layout_label('Percent of impairement').":";
            }

            if ($key == 'findings_text' ) 
            { 
                echo xl_layout_label('Describe findings and results').":";
            }

            if ($key == 'work_status' ) 
            { 
                echo xl_layout_label('Is patient working now').":";
            }

            if ($key == 'work_restrictions' ) 
            { 
                echo xl_layout_label('Are there work restrictions').":";
            }

            if ($key == 'work_restrictions_text' ) 
            { 
                echo xl_layout_label('If Yes, describe restrictions').":";
            }

            if ($key == 'restrictions_period' ) 
            { 
                echo xl_layout_label('How long will the work restrictions apply').":";
            }

            if ($key == 'work_options' ) 
            { 
                echo xl_layout_label('Can Patient return to work?(check only one)').":";
            }

            if ($key == 'work_option_text' ) 
            { 
                echo xl_layout_label('Explain, or specify date').":";
            }

            if ($key == 'limit_options' ) 
            { 
                echo xl_layout_label('Lmitations').":";
            }

            if ($key == 'limits_quant_text' ) 
            { 
                echo xl_layout_label('Describe the limitations').":";
            }

            if ($key == 'limits_period' ) 
            { 
                echo xl_layout_label('How long will this limitations apply').":";
            }

            if ($key == 'return_work_discuss' ) 
            { 
                echo xl_layout_label('Discussed patient returning to work with').":";
            }

            if ($key == 'benefit_rehab' ) 
            { 
                echo xl_layout_label('Would the patient benefit from vocational rehabilitation').":";
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


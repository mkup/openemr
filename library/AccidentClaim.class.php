<?php

// Copyright (C) 2012-2016 Mark Kuperman <mkuperman@mi-10.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2

require_once ('Claim.class.php');
require_once (dirname(__FILE__) . '/../custom/code_types.inc.php');

function date0($dte) {
    return (strcmp(substr($dte,0,10), "0000-00-00") == 0);
}

Class AccidentClaim extends Claim {

    var $diagTexts;         // Array of ICD9 long descriptions
    var $accident;          // Accident form
    var $c4Tests;
    var $c4Refs;
    var $c4Limits;

    function AccidentClaim($pid, $encounter_id) {
        parent::Claim($pid, $encounter_id);
        //  Load ICD9 descriptions
        foreach ($this->diags as $key => $cde) {
            $codes .= "ICD9:" . $cde . ';';
        }
        $desc = lookup_code_descriptions($codes);
        $descAr = explode(';', $desc);
        $i = 0;
        foreach ($this->diags as $key => $cde) {
            $this->diagTexts[$key] = $descAr[$i];
            $i++;
        }
    }
    
    function nf3Initial() {
        // Find the latest Auto Accident Initial form
        $sql = "SELECT fpa.* FROM forms JOIN form_acc_auto_initial AS fpa " .
                "ON fpa.id = forms.form_id WHERE " .
                "forms.pid = '{$this->pid}' AND " .
                "forms.deleted = 0 AND " .
                "forms.formdir = 'accident_auto_initial' " .
                "ORDER BY fpa.date_accident desc, forms.id desc";
        
        $res = sqlQuery($sql);
        if ($res)
            $this->accident = $res;
        else
            $this->accident = array();
    }

    //  Overwright INITIAL accident data with the FOLLOW-UP form data
    function nf3followup() {
        $this->nf3Initial();
        
        $sql = "SELECT fpa.* FROM forms JOIN form_acc_auto_followup AS fpa " .
                "ON fpa.id = forms.form_id WHERE " .
                "forms.encounter = '{$this->encounter_id}' AND " .
                "forms.pid = '{$this->pid}' AND " .
                "forms.deleted = 0 AND " .
                "forms.formdir = 'accident_auto_followup' " .
                "ORDER BY forms.id desc";
        $res = sqlQuery($sql);
        if ($res) {
            foreach($res as $k=>$v) 
                if (!empty($v))   $this->accident[$k] = $v;
        }
    }
    
    function c4Initial() {
        // Find the latest Auto Accident Initial form
         $sql = "SELECT fpa.* FROM forms JOIN form_workcomp_followup AS fpa " .
                "ON fpa.id = forms.form_id WHERE " .
                "forms.pid = '{$this->pid}' AND " .
                "forms.deleted = 0 AND " .
                "forms.formdir = 'workcomp_followup' " .
                "ORDER BY forms.id desc";
       $res = sqlQuery($sql);
        if ($res)
            $this->accident = $res;
        else 
            $this->accident = array();
    }

    function c4followup() {
        $this->c4Initial();
        
        $sql = "SELECT fpa.* FROM forms JOIN form_workcomp_followup AS fpa " .
                "ON fpa.id = forms.form_id WHERE " .
                "forms.encounter = '{$this->encounter_id}' AND " .
                "forms.pid = '{$this->pid}' AND " .
                "forms.deleted = 0 AND " .
                "forms.formdir = 'workcomp_followup' " .
                "ORDER BY forms.id desc";
        $res = sqlQuery($sql);
        if ($res) {
            foreach($res as $k=>$v) 
                if (!empty($v))   $this->accident[$k] = $v;
        }
        $this->parseCheckBoxes();
    }
    
    function parseCheckBoxes() {
        // C4Tests
        $this->c4Tests = array();
        $t = $this->accident['test_options'];
        if (!empty($t)) {
            $ar = explode('|', $t);
            foreach($ar as $t) {
                $a = explode('_', $t);
                $this->c4Tests[$a[1]] = true;
            }
        }
        // C4Refs
        $this->c4Refs = array();
        $t = $this->accident['ref_options'];
        if (!empty($t)) {
            $ar = explode('|', $t);
            foreach($ar as $t) {
                $a = explode('_', $t);
                $this->c4Refs[$a[1]] = true;
            }
        }
        // C4Limits
        $this->c4Limits = array();
        $t = $this->accident['limit_options'];
        if (!empty($t)) {
            $ar = explode('|', $t);
            foreach($ar as $t) {
                $a = explode('_', $t);
                $this->c4Limits[$a[1]] = true;
            }
        }
    }
    
    function codeText($cde) {
        return $this->diagTexts[$cde];
    }

    function accidentDate() {
        return !date0($this->accident['date_accident']) ? $this->accident['date_accident'] : false;
    }

    function claimNumber() {
        return trim($this->accident['claim_number']);
    }

    function firstSymptomDate() {
         return !date0($this->accident['date_symptom']) ? $this->accident['date_symptom'] : false;
   }

    function firstVisitDate() {
        return !date0($this->accident['date_visit1']) ? $this->accident['date_visit1'] : false;
    }

    function priorCondition() {
        return !empty($this->accident['same_condition']) ? $this->accident['same_condition'] : false;
    }

    function priorText() {
        return !empty($this->accident['same_text']) ? $this->accident['same_text'] : false;
    }
    
    function solelyAuto() {
        return !empty($this->accident['solely_accident']) ? $this->accident['solely_accident'] : false;
    }

    function solelyText() {
        return !empty($this->accident['solely_text']) ? $this->accident['solely_text'] : false;
    }
    
    function employmentRelated() {
        return !empty($this->accident['due_employment']) ? $this->accident['due_employment'] : false;
    }

    function permanentDisability() {
        return $this->accident['disability'];      // Default is NA
    }

    function disabilityText() {
        return !empty($this->accident['disability_text']) ? $this->accident['disability_text'] : false;
    }
    
    function disabilityFrom() {
        return !date0($this->accident['disable_from']) ? $this->accident['disable_from'] : false;
    }

    function disabilityTo() {
        return !date0($this->accident['disable_to']) ? $this->accident['disable_to'] : false;
    }

    function returnToWork() {
        return !date0($this->accident['return_work']) ? $this->accident['return_work'] : false;
    }

    function needRehab() {
        return !empty($this->accident['need_rehab']) ? $this->accident['need_rehab'] : false;
    }

    function rehabText() {
        return !empty($this->accident['rehab_text']) ? $this->accident['rehab_text'] : false;
    }
    
    function providerStateLicense() {
        return $this->provider['state_license_number'];
    }

    function patientSSN() {
        return trim($this->patient_data['ss']);
    }

    function WCBCaseNumber() {
        return trim($this->accident['WCBCase']);
//        return "123456789";
    }
    
    function injuryJobDescription() {
//        return trim($this->accident['injury_job']);
        return "1234567890123456789012345678901234567890";
    }
    
    function injuryActivity() {
//        return trim($this->accident['injury_activity']);
        return "1234567890123456789012345678901234567890123456789012345678901234567890";
    }

    function injuryEmployer() {
//        return trim($this->accident['injury_employer']);
        return "Bad ACME Company";
    }

    function injuryEmployerPhone() {
//        return trim($this->accident['injury_emp_phone']);
        return "718-555-5555";
    }

    function injuryEmployerAddress() {
//        return trim($this->accident['injury_emp_address']);
        return "55 Corporate Park, Brooklyn NY 11235";
    }

    function facilityPhone() {
        return trim($this->facility['phone']) ? $this->facility['phone'] : '';
    }

    function docWCBRating() {
        return trim($this->accident['WCBRating']);
//        return "Great";
    }

    function injuryDescription() {
        return trim($this->accident['injury_description']);
//        return "1234567890123456789012345678901234567890";
    }

    function historyLearn() {
//        return $this->accident['history_learn'];
        return "P";
    }

    function injuryLearnText() {
//        return $this->accident['history_learn_text'];
        return "123456789012345678901234567890";
    }

    function historyDiffMD() {
//        return $this->accident['history_diffMD'];
        return "N";
    }

    function historyDiffMDtext() {
//        return trim($this->accident['history_diffMD_text']);
        return "12345678901234567891234567890";
    }

    function historyTreated() {
//        return $this->accident['history_treated'];
        return "N";
    }

    function historyTreatedWhen() {
//        return trim($this->accident['history_treated_when']);
        return "last year";
    }

    function examNumbness() {
//        return trim($this->accident['exam_numbness']);
        return "Y";
    }

    function examNumbnessText() {
//        return trim($this->accident['exam_numbness_text']);
        return "hurts";
    }

    function examSwelling() {
//        return trim($this->accident['exam_swelling']);
        return "Y";
    }

    function examSwellingText() {
//        return trim($this->accident['exam_swelling_text']);
        return "hurts";
    }

    function examPain() {
//        return trim($this->accident['exam_pain']);
        return "Y";
    }

    function examPainText() {
//        return trim($this->accident['exam_pain_text']);
        return "hurts";
    }

    function examWeakness() {
//        return trim($this->accident['exam-Weakness']);
        return "Y";
    }

    function examWeaknessText() {
//        return trim($this->accident['exam_weakness_text']);
        return "hurts";
    }

    function examStiffness() {
//        return trim($this->accident['exam_stiffness']);
        return "Y";
    }

    function examStiffnessText() {
//        return trim($this->accident['exam_stiffness_text']);
        return "hurts";
    }

    function examOther() {
//        return trim($this->accident['exam_other']);
        return "Y";
    }

    function examOtherText() {
//        return trim($this->accident['exam_other_text']);
        return "hurts";
    }

    function examAbrasion() {
//        return trim($this->accident['exam_abrasion']);
        return "Y";
    }

    function examAbrasionText() {
//        return trim($this->accident['exam_abrasion_text']);
        return "hurts";
    }

    function examAmputation() {
//        return trim($this->accident['exam_amputation']);
        return "Y";
    }

    function examAmputationText() {
//        return trim($this->accident['exam_amputation_text']);
        return "hurts";
    }

    function examAvulsion() {
//        return trim($this->accident['exam_avulsion']);
        return "Y";
    }

    function examAvulsionText() {
//        return trim($this->accident['exam_avulsion_text']);
        return "hurts";
    }

    function examBite() {
//        return trim($this->accident['exam_bite']);
        return "Y";
    }

    function examBiteText() {
//        return trim($this->accident['exam_bite_text']);
        return "hurts";
    }

    function examBurn() {
//        return trim($this->accident['exam_burn']);
        return "Y";
    }

    function examBurnText() {
//        return trim($this->accident['exam_burn_text']);
        return "hurts";
    }

    function examContusion() {
//        return trim($this->accident['exam_contusion']);
        return "Y";
    }

    function examContusionText() {
//        return trim($this->accident['exam_contusion_text']);
        return "hurts";
    }

    function examCrush() {
//        return trim($this->accident['exam_crush']);
        return "Y";
    }

    function examCrushText() {
//        return trim($this->accident['exam_crush_text']);
        return "hurts";
    }

    function examDerma() {
//        return trim($this->accident['exam_derma']);
        return "Y";
    }

    function examDermaText() {
//        return trim($this->accident['exam_derma_text']);
        return "hurts";
    }

    function examDislocation() {
//        return trim($this->accident['exam_dislocation']);
        return "Y";
    }

    function examDislocationText() {
//        return trim($this->accident['exam_dislocation_text']);
        return "hurts";
    }

    function examFracture() {
//        return trim($this->accident['exam_fracture']);
        return "Y";
    }

    function examFractureText() {
//        return trim($this->accident['exam_fracture_text']);
        return "hurts";
    }

    function examHearing() {
//        return trim($this->accident['exam_hearing']);
        return "Y";
    }

    function examHearingText() {
//        return trim($this->accident['exam_hearing_text']);
        return "hurts";
    }

    function examHernia() {
//        return trim($this->accident['exam_hernia']);
        return "Y";
    }

    function examHerniaText() {
//        return trim($this->accident['exam_hernia_text']);
        return "hurts";
    }

    function examInfect() {
//        return trim($this->accident['exam_infect']);
        return "Y";
    }

    function examInfectText() {
//        return trim($this->accident['exam_infect_text']);
        return "hurts";
    }

    function examInhalation() {
//        return trim($this->accident['exam_inhalation']);
        return "Y";
    }

    function examInhalationText() {
//        return trim($this->accident['exam_inhalation_text']);
        return "hurts";
    }

    function examLaceration() {
//        return trim($this->accident['exam_laceration']);
        return "Y";
    }

    function examLacerationText() {
//        return trim($this->accident['exam_laceration_text']);
        return "hurts";
    }

    function examNeedle() {
//        return trim($this->accident['exam_needle']);
        return "Y";
    }

    function examNeedleText() {
//        return trim($this->accident['exam_needle_text']);
        return "hurts";
    }

    function examPoison() {
//        return trim($this->accident['exam_poison']);
        return "Y";
    }

    function examPoisonText() {
//        return trim($this->accident['exam_poison_text']);
        return "hurts";
    }

    function examPsych() {
//        return trim($this->accident['exam_psych']);
        return "Y";
    }

    function examPsychText() {
//        return trim($this->accident['exam_psych_text']);
        return "hurts";
    }

    // F.3. Nature of Injury
    function examPunct() {
//        return trim($this->accident['exam_punct']);
        return "Y";
    }

    function examPunctText() {
//        return trim($this->accident['exam_punct_text']);
        return "hurts";
    }

    function examRepeat() {
//        return trim($this->accident['exam_repeat']);
        return "Y";
    }

    function examRepeatText() {
//        return trim($this->accident['exam_repeat_text']);
        return "hurts";
    }

    function examSpinal() {
//        return trim($this->accident['exam_spinal']);
        return "Y";
    }

    function examSpinalText() {
//        return trim($this->accident['exam_spinal_text']);
        return "hurts";
    }

    function examSprain() {
//        return trim($this->accident['exam_sprain']);
        return "Y";
    }

    function examSprainText() {
//        return trim($this->accident['exam_sprain_text']);
        return "hurts";
    }

    function examTorn() {
//        return trim($this->accident['exam_torn']);
        return "Y";
    }

    function examTornText() {
//        return trim($this->accident['exam_torn_text']);
        return "hurts";
    }

    function examVision() {
//        return trim($this->accident['exam_vision']);
        return "Y";
    }

    function examVisionText() {
//        return trim($this->accident['exam_vision_text']);
        return "hurts";
    }

    function examTypeOther() {
//        return trim($this->accident['exam_type_other']);
        return "Y";
    }

    function examTypeOtherText() {
//        return trim($this->accident['exam_type_other_text']);
        return "hurts";
    }

    function objNone() {
//        return trim($this->accident['obj_none']);
        return false;
    }

    function objBruise() {
//        return trim($this->accident['obj_bruise']);
        return "Y";
    }

    function objBruiseText() {
//        return trim($this->accident['obj_bruise_text']);
        return "visible";
    }

    function objBurns() {
//        return trim($this->accident['obj_burns']);
        return "Y";
    }

    function objBurnsText() {
//        return trim($this->accident['obj_burns_text']);
        return "visible";
    }

    function objCrepit() {
//        return trim($this->accident['obj_crepit']);
        return "Y";
    }

    function objCrepitText() {
//        return trim($this->accident['obj_crepit_text']);
        return "visible";
    }

    function objDeform() {
//        return trim($this->accident['obj_deform']);
        return "Y";
    }

    function objDeformText() {
//        return trim($this->accident['obj_deform_text']);
        return "visible";
    }

    function objEdema() {
//        return trim($this->accident['obj_edema']);
        return "Y";
    }

    function objEdemaText() {
//        return trim($this->accident['obj_edema_text']);
        return "visible";
    }

    function objHematoma() {
//        return trim($this->accident['obj_hematoma']);
        return "Y";
    }

    function objHematomaText() {
//        return trim($this->accident['obj_hematoma_text']);
        return "visible";
    }

    function objJoint() {
//        return trim($this->accident['obj_joint']);
        return "Y";
    }

    function objJointText() {
//        return trim($this->accident['obj_joint_text']);
        return "visible";
    }

    function objLacerat() {
//        return trim($this->accident['obj_lacerat']);
        return "Y";
    }

    function objLaceratText() {
//        return trim($this->accident['obj_lacerat_text']);
        return "visible";
    }

    function objPain() {
//        return trim($this->accident['obj_pain']);
        return "Y";
    }

    function objPainText() {
//        return trim($this->accident['obj_pain_text']);
        return "visible";
    }

    function objScar() {
//        return trim($this->accident['obj_scar']);
        return "Y";
    }

    function objScarText() {
//        return trim($this->accident['obj_scar_text']);
        return "visible";
    }

    function objOther() {
//        return trim($this->accident['obj_other']);
        return "Y";
    }

    function objOtherText() {
//        return trim($this->accident['obj_other_text']);
        return "visible";
    }

    function objNeuro() {
//        return trim($this->accident['obj_neuro']);
        return "Y";
    }

    function objAbnormal() {
//        return trim($this->accident['obj_abnormal']);
        return "Y";
    }

    function objActive() {
//        return trim($this->accident['obj_active']);
        return "Y";
    }

    function objActiveText() {
//        return trim($this->accident['obj_active_text']);
        return "visible";
    }

    function objPassive() {
//        return trim($this->accident['obj_passive']);
        return "Y";
    }

    function objPassiveText() {
//        return trim($this->accident['obj_passive_text']);
        return "visible";
    }

    function objGait() {
//        return trim($this->accident['obj_gait']);
        return "Y";
    }

    function objGaitText() {
//        return trim($this->accident['obj_gait_text']);
        return "visible";
    }

    function objPalpable() {
//        return trim($this->accident['obj_palpable']);
        return "Y";
    }

    function objPalpableText() {
//        return trim($this->accident['obj_palpable_text']);
        return "visible";
    }

    function objReflex() {
//        return trim($this->accident['obj_reflex']);
        return "Y";
    }

    function objReflexText() {
//        return trim($this->accident['obj_reflex_text']);
        return "visible";
    }

    function objSensation() {
//        return trim($this->accident['obj_sensation']);
        return "Y";
    }

    function objSensationText() {
//        return trim($this->accident['obj_sensation_text']);
        return "visible";
    }

    function objStrength() {
//        return trim($this->accident['obj_strength']);
        return "Y";
    }

    function objStrengthText() {
//        return trim($this->accident['obj_strength_text']);
        return "visible";
    }

    function objWasting() {
//        return trim($this->accident['obj_wasting']);
        return "Y";
    }

    function objWastingText() {
//        return trim($this->accident['obj_wasting_text']);
        return "visible";
    }

    function visitTests() {
        return trim($this->accident['visit_tests']);
//        return "visible";
    }

    function visitTreatments() {
        return trim($this->accident['visit_treatments']);
//        return "12345678901234567890123456781234567892345678";
    }

    function visitPrognosis() {
//        return trim($this->accident['visit_prognosis']);
        return "12345678901234567890123456781234567892345678";
    }

    function injuryPreexist() {
//        return trim($this->accident['injury_preexist']);
        return "Y";
    }

    function injuryPreexistText() {
//        return trim($this->accident['injury_preexist_text']);
        return "visible";
    }

    function conditionCaused() {
        return $this->accident['condition_caused'];
//        return "Y";
    }

    function conditionComplaints() {
        return $this->accident['condition_complaints'];
//        return "Y";
    }

    function conditionHistory() {
        return $this->accident['condition_history'];
//        return "Y";
    }

    function percentImpaired() {
        return trim($this->accident['percent_impaired']);
//        return "30";
    }

    function findingsText() {
        return trim($this->accident['findings_text']);
//        return "visible";
    }

    function planTreatmentText() {
        return trim($this->accident['plan_treatment_text']);
//        return "treat him thoroughly";
    }

    function medsOTCText() {
//        return trim($this->accident['meds_otc_text']);
        return "tulenol";
    }

    function medsPrescribedText() {
//        return trim($this->accident['meds_prescribed_text']);
        return "vicodin";
    }

     function medsRestrictions() {
//        return $this->accident['meds_restrictions'];
        return "Y";
    }

   function medsRestrictionsText() {
//        return trim($this->accident['meds_restrictions_text']);
        return "no drinking";
    }

    function needTestRefs() {
        return trim($this->accident['need_test_refs']);
//        return "Y";
    }

    function needTest($test) {
        return !empty($this->c4Tests[$test]) ? $this->c4Tests[$test] : false;
    }

    function needMRIText() {
//        return trim($this->accident['need_MRI_text']);
        return "visible";
    }

    function needLabsText() {
//        return trim($this->accident['need_labs_text']);
        return "visible";
    }

    function needXrayText() {
//        return trim($this->accident['need_Xray_text']);
        return "visible";
    }

    function needOtherText() {
//        return trim($this->accident['need_other_text']);
        return "visible";
    }

    function needRef($ref) {
        return !empty($this->c4Refs[$ref]) ? $this->c4Refs[$ref] : false;
    }

    function needSpecialistText() {
//        return trim($this->accident['need_specialist_text']);
        return "yes, please";
    }

    function needRefOtherText() {
//        return trim($this->accident['need_ref_other_text']);
        return "very much";
    }

    function deviceCane() {
//        return trim($this->accident['device_cane']);
        return "Y";
    }

    function deviceCrutches() {
//        return trim($this->accident['device_crutches']);
        return "Y";
    }

    function deviceOrtho() {
//        return trim($this->accident['device_ortho']);
        return "Y";
    }

    function deviceWalker() {
//        return trim($this->accident['device_walker']);
        return "Y";
    }

    function deviceWheel() {
//        return trim($this->accident['device_wheel']);
        return "Y";
    }

    function deviceOther() {
//        return trim($this->accident['device_other']);
        return "Y";
    }

    function deviceOtherText() {
//        return trim($this->accident['device_other_text']);
        return "auto";
    }

    function nextAppointment() {
        $t = explode('_', trim($this->accident['next_appointment']));
        return $t[1];
//        return "2";
    }

     function adhereGuide() {
//        return trim($this->accident['adhere_guide']);
        return "Y";
    }

    function guideYesText() {
//        return trim($this->accident['guide_yes_text']);
        return "because";
    }

    function guideNoText() {
//        return trim($this->accident['guide_no_text']);
        return "because";
    }

     function workMissed() {
//        return $this->accident['work_missed'];
        return "Y";
    }

    function workMissedDate() {
//        return trim($this->accident['work_missed_date']);
        return "Jan, 2012";
    }

     function workStatus() {
        return $this->accident['work_status'];
//        return "Y";
    }

     function workCasual() {
        return $this->accident['work_casual'];
//        return "Y";
    }

     function workLimited() {
        return $this->accident['work_limited'];
//        return "Y";
    }

     function workReturnOptions() {
        $t =  explode('_', trim($this->accident['work_options']));
        return $t[1];
    }

    function workReturnText() {
        return trim($this->accident['work_return_text']);
//        return "because";
    }

    function limits($lim) {
        return !empty($this->c4Limits[$lim]) ? $this->c4Limits[$lim] : false;
    }

    function limitsOtherText() {
//        return trim($this->accident['limits_other_text']);
        return "tomorrow";
    }

    function limitsQuantText() {
        return trim($this->accident['limits_quant_text']);
//        return "tomorrow";
    }

     function limitsPeriod() {
        $t =  explode('_', trim($this->accident['limits_period']));
        return $t[1];
    }

     function returnWorkDiscuss() {
        $t = explode('_', trim($this->accident['return_work_discuss']));
        return $t[1];
//        return "Patient";
    }

    function examDates() {
        return $this->serviceDate();
    }

    function insuranceWCode() {
      return trim($this->accident['insurance_W_code']);
//        return "456789";
    }
    
    function examChanges() {
      return trim($this->accident['exam_changes']);
//        return "No significant Changes";
    }
    
    function additionalBodyParts() {
      return trim($this->accident['additioanl_body_parts']);
//        return "head hurts";
    }
    
    function planChanges() {
      return trim($this->accident['plan_changes']);
//        return "No plan Changes";
    }
    
     function workRestrictions() {
        return trim($this->accident['work_restrictions']);
//        return "Y";
    }

    function restrictionsText() {
        return trim($this->accident['work_restrictions_text']);
//        return "take it easy";
    }

    function restrictionsPeriod() {
        $t =  explode('_', trim($this->accident['restrictions_period']));
        return $t[1];
    }

    function benefitRehab() {
        return ($this->accident['benefit_rehab']);
//        return "N";
    }

    function patientAccount() {
        return $this->patient_data['pubpid'];
    }
    
    function doctorSpecialty() {
        return trim($this->provider['specialty']);
    }
    
    function doctorTitle() {
        return $this->provider['title'];
    }
    
    function WCBAuth() {
        return $this->providerStateLicense();
    }
    
    function WCBRating() {
        return $this->provider['valedictory'];
    }
  function diagCodesArray($prockey) {
    $dia = array();
    $da = $this->diagArray();
    $atmp = explode(':', $this->procs[$prockey]['justify']);
    foreach ($atmp as $tmp) {
      if (!empty($tmp)) {
        $code_data = explode('|',$tmp);
        if (!empty($code_data[1])) {
          //Strip the prepended code type label
          $diag = $code_data[1];
        }
        else {
          //No prepended code type label
          $diag = $code_data[0];
        }
        $dia[] = $diag;
      }
    }
    return $dia;
  }
}

?>

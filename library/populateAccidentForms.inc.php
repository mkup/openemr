<?php
// Copyright (C) 2012-2016 Mark Kuperman <mkuperman@mi-10.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2


function formatDate($dte) {
    $t = str_replace('-', '', $dte);
    return substr($t, 4, 2) . '/' . substr($t, 6, 2) . '/' . substr($t, 0, 4);
}

function populateC4($claim) {

    $cbYes = 'Yes';
    $Y = 'Y';
    $N = 'N';
    $map = array();

    // C4.2 only
    $map['Exam Dates'] = formatDate($claim->examDates());               //*** new
    $map['Practice Name'] = $claim->facilityName();
    $map['Insurance Code W'] = $claim->insuranceWCode();    //*** new
    if ($val = $claim->examChanges()) {                     //*** new
        $map['Exam Changes1'] = substr($val, 0, 100);
        $map['Exam Changes2'] = substr($val, 100, 140);
    }

    $map['Account #'] = $claim->patientAccount();

    if ($val = $claim->additionalBodyParts())               //*** new
        $map['Obj Additional'] = substr($val, 0, 80);

    if ($val = $claim->planChanges())                       //*** new
        $map['Plan Changes'] = substr($val, 0, 140);

    $val = $claim->benefitRehab();                          //*** new
    $map['CB Benefit ' . $val] = $cbYes;

    // A. Patient Information
    $map['Name'] = $claim->patientLastName() . ', ' . $claim->patientFirstName();

    $map['ssn'] = $claim->patientSSN();

    $map['Phone'] = $claim->patientPhone();

    $map['WCB Case'] = $claim->WCBCaseNumber();

    $map['Address'] = $claim->patientStreet() . ',   ' .
            $claim->patientCity() . ',  ' .
            $claim->patientState() . '  ' .
            $claim->patientZip();

    $map['Insurance Claim'] = $claim->claimNumber();

    if ($val = $claim->accidentDate())
        $map['Injury Date'] = formatDate($val);

    $map['dob'] = formatDate($claim->patientDOB());

    $val = $claim->patientSex();        //*** Select gender
    if ($val = 'M')
        $map['CB Male'] = $cbYes;
    else
        $map['CB Female'] = $cbYes;

    $map['Injury JobDesc'] = $claim->injuryJobDescription();

    $val = $claim->injuryActivity();
    $map['Injury Activity1'] = substr($val, 0, 49);
    $map['Injury Activity2'] = substr($val, 49, 100);

    // B. Employer Information
    $map['Injury Employer'] = $claim->injuryEmployer();
    $map['Injury EmpPhone'] = $claim->injuryEmployerPhone();
    $map['Injury EmpAddress'] = $claim->injuryEmployerAddress();

    // C. Doctor's Information
    $map['Doctor Name'] = $claim->providerLastName() . ', ' . $claim->providerFirstName();

    $map['Doctor WCBAuth'] = $claim->providerStateLicense();

    $map['CB Physician'] = $cbYes;

    $map['Doctor Address'] = $claim->facilityStreet() . ', ' . $claim->facilityCity() . ', ' . $claim->facilityZip();

    $map['Doctor WCBRating'] = $claim->docWCBRating();   //***New  Find out what this is

    $map['Doctor Address'] = $claim->facilityStreet() . ', ' . $claim->facilityCity() . ', ' . $claim->facilityState() . '  ' . $claim->facilityZip();

    $map['Doctor Billing Address'] = $claim->billingFacilityStreet() . ', ' . $claim->billingFacilityCity() . ', ' . $claim->billingFacilityState() . '  ' . $claim->billingFacilityZip();

    $map['Doctor Phone'] = $claim->facilityPhone();

    $map['Doctor Phone Billing'] = $claim->billingContactPhone();

    $map['Doctor NPI'] = $claim->providerNPI();

    $map['Doctor EIN'] = $claim->facilityETIN();

    $map['CB EIN'] = $cbYes;

    // D. Billing Information
    $map['Insurance Name'] = $claim->payerName();

    $map['Insurance Address'] = $claim->payerStreet(0) . ', ' . $claim->payerCity(0) . ', ' . $claim->payerState(0) . ' ' . $claim->payerZip(0);

    $i = 1;
    foreach ($claim->diagTexts as $cde => $txt) {
        $map['ICD Code' . $i] = $cde;
        $map['ICD Desc' . $i] = $txt;
        $i++;
    }

    //  Page 2
    $val = $claim->serviceDate();
    $m = substr($val, 4, 2);
    $d = substr($val, 6, 2);
    $y = substr($val, 2, 2);
    $zip = $claim->facilityZip();
    $pos = $claim->facilityPOS();
    $totalCh = 0;
    for ($j = 0; $j < $claim->procCount(); $j++) {
        $i = $j + 1;
        $map['CPT From MM' . $i] = $m;
        $map['CPT From DD' . $i] = $d;
        $map['CPT From YY' . $i] = $y;
        $map['CPT To MM' . $i] = $m;
        $map['CPT To DD' . $i] = $d;
        $map['CPT To YY' . $i] = $y;
        $map['CPT POS' . $i] = $pos;
        $map['CPT' . $i] = $claim->cptCode($j);
        $map['CPT Mod' . $i] = $claim->cptModifier($j);
        $icds = implode(';', $claim->diagCodesArray($j));
        $map['CPT icd' . $i] = $icds;
        $val = $claim->cptCharges($j);
        $totalCh += $val;
        $map['CPT Charge' . $i] = money_format('%.2n', $val);
        $map['CPT Units' . $i] = '1';
        $map['CPT Zip' . $i] = $zip;
    }
    $map['CPT Charge Total'] = money_format('%.2n', $totalCh);

    //  E. History
    if ($val = $claim->injuryDescription()) {
        $map['Injury Description1'] = substr($val, 0, 60);
        $map['Injury Description2'] = substr($val, 60, 130);
        $map['Injury Description3'] = substr($val, 190, 130);
    }

    $val = $claim->historyLearn();
    if ($val == 'P')
        $map['CB Injury Learned Patient'] = $cbYes;          //***  need to clarify
    else if ($val == 'R')
        $map['CB Injury Learned Records'] = $cbYes;
    else {
        $map['CB Injury Learned Other'] = $cbYes;
        $map['Injury Learaned text'] = $claim->injuryLearnText();
    }

    if (($val = $claim->historyDiffMD()) && ($val == $Y)) {
        $map['CB Injury diff MD Y'] = $cbYes;
        $map['Injury diff MD text'] = substr($claim->historyDiffMDtext(), 0, 140);
    }
    else
        $map['CB Injury diff MD N'] = $cbYes;

    if (($val = $claim->historyTreated()) && ($val == $Y)) {
        $map['CB Treated prev Y'] = $cbYes;
        $map['Injury Treated prev when'] = substr($claim->historyTreatedWhen(), 0, 30);
    }
    else
        $map['CB Treated prev No'] = $cbYes;

    //   F. Exam Information
    $map['Exam Date'] = formatDate($claim->serviceDate());

    // F.2. Subjective complaints
    $val = $claim->examNumbness();
    if ($val && $val == $Y) {
        $map['CB Exam Numbness'] = $cbYes;
        $map['Exam Numbness text'] = substr($claim->examNumbnessText(), 0, 30);
    }

    $val = $claim->examSwelling();
    if ($val && $val == $Y) {
        $map['CB Exam Swelling'] = $cbYes;
        $map['Exam Swelling text'] = substr($claim->examSwellingText(), 0, 50);
    }

    $val = $claim->examPain();
    if ($val && $val == $Y) {
        $map['CB Exam Pain'] = $cbYes;
        $map['Exam Pain text'] = substr($claim->examPainText(), 0, 50);
    }

    $val = $claim->examWeakness();
    if ($val && $val == $Y) {
        $map['CB Exam Weakness'] = $cbYes;
        $map['Exam Weakness text'] = substr($claim->examWeaknessText(), 0, 50);
    }

    $val = $claim->examStiffness();
    if ($val && $val == $Y) {
        $map['CB Exam Stiffness'] = $cbYes;
        $map['Exam Stiffness text'] = substr($claim->examStiffnessText(), 0, 50);
    }

    $val = $claim->examOther();
    if ($val && $val == $Y) {
        $map['CB Exam Other'] = $cbYes;
        $map['Exam Other text'] = substr($claim->examOtherText(), 0, 40);
    }

    // F.3. Nature of Injury
    $val = $claim->examAbrasion();
    if ($val && $val == $Y) {
        $map['CB Exam Abrasion'] = $cbYes;
        $map['Exam Abrasion text'] = substr($claim->examAbrasionText(), 0, 50);
    }

    $val = $claim->examAmputation();
    if ($val && $val == $Y) {
        $map['CB Exam Amputation'] = $cbYes;
        $map['Exam Amputation text'] = substr($claim->examAmputationText(), 0, 50);
    }

    $val = $claim->examAvulsion();
    if ($val && $val == $Y) {
        $map['CB Exam Avulsion'] = $cbYes;
        $map['Exam Avulsion text'] = substr($claim->examAvulsionText(), 0, 50);
    }

    $val = $claim->examBite();
    if ($val && $val == $Y) {
        $map['CB Exam Bite'] = $cbYes;
        $map['Exam Bite text'] = substr($claim->examBiteText(), 0, 50);
    }

    $val = $claim->examBurn();
    if ($val && $val == $Y) {
        $map['CB Exam Burn'] = $cbYes;
        $map['Exam Burn text'] = substr($claim->examBurnText(), 0, 50);
    }

    $val = $claim->examContusion();
    if ($val && $val == $Y) {
        $map['CB Exam Contusion'] = $cbYes;
        $map['Exam Contusion text'] = substr($claim->examContusionText(), 0, 50);
    }

    $val = $claim->examCrush();
    if ($val && $val == $Y) {
        $map['CB Exam Crush'] = $cbYes;
        $map['Exam Crush text'] = substr($claim->examCrushText(), 0, 50);
    }

    $val = $claim->examDerma();
    if ($val && $val == $Y) {
        $map['CB Exam Derma'] = $cbYes;
        $map['Exam Derma text'] = substr($claim->examDermaText(), 0, 50);
    }

    $val = $claim->examDislocation();
    if ($val && $val == $Y) {
        $map['CB Exam Dislocation'] = $cbYes;
        $map['Exam Dislocation text'] = substr($claim->examDislocationText(), 0, 50);
    }

    $val = $claim->examFracture();
    if ($val && $val == $Y) {
        $map['CB Exam Fracture'] = $cbYes;
        $map['Exam Fracture text'] = substr($claim->examFractureText(), 0, 50);
    }

    $val = $claim->examHearing();
    if ($val && $val == $Y) {
        $map['CB Exam Hearing'] = $cbYes;
        $map['Exam Hearing text'] = substr($claim->examHearingText(), 0, 50);
    }

    $val = $claim->examHernia();
    if ($val && $val == $Y) {
        $map['CB Exam Hernia'] = $cbYes;
        $map['Exam Hernia text'] = substr($claim->examHerniaText(), 0, 50);
    }

    $val = $claim->examInfect();
    if ($val && $val == $Y) {
        $map['CB Exam Infect'] = $cbYes;
        $map['Exam Infect text'] = substr($claim->examInfectText(), 0, 50);
    }

    $val = $claim->examInhalation();
    if ($val && $val == $Y) {
        $map['CB Exam Inhalation'] = $cbYes;
        $map['Exam Inhalation text'] = substr($claim->examInhalationText(), 0, 50);
    }

    $val = $claim->examLaceration();
    if ($val && $val == $Y) {
        $map['CB Exam Laceration'] = $cbYes;
        $map['Exam Laceration text'] = substr($claim->examLacerationText(), 0, 50);
    }

    $val = $claim->examNeedle();
    if ($val && $val == $Y) {
        $map['CB Exam Needle'] = $cbYes;
        $map['Exam Needle text'] = substr($claim->examNeedleText(), 0, 50);
    }

    $val = $claim->examPoison();
    if ($val && $val == $Y) {
        $map['CB Exam Poison'] = $cbYes;
        $map['Exam Poison text'] = substr($claim->examPoisonText(), 0, 50);
    }

    $val = $claim->examPsych();
    if ($val && $val == $Y) {
        $map['CB Exam Psych'] = $cbYes;
        $map['Exam Psych text'] = substr($claim->examPsychText(), 0, 50);
    }

    $val = $claim->examPunct();
    if ($val && $val == $Y) {
        $map['CB Exam Punct'] = $cbYes;
        $map['Exam Punct text'] = substr($claim->examPunctText(), 0, 50);
    }

    $val = $claim->examRepeat();
    if ($val && $val == $Y) {
        $map['CB Exam Repeat'] = $cbYes;
        $map['Exam Repeat text'] = substr($claim->examRepeatText(), 0, 50);
    }

    $val = $claim->examSpinal();
    if ($val && $val == $Y) {
        $map['CB Exam Spinal'] = $cbYes;
        $map['Exam Spinal text'] = substr($claim->examSpinalText(), 0, 50);
    }

    $val = $claim->examSprain();
    if ($val && $val == $Y) {
        $map['CB Exam Sprain'] = $cbYes;
        $map['Exam Sprain text'] = substr($claim->examSprainText(), 0, 50);
    }

    $val = $claim->examTorn();
    if ($val && $val == $Y) {
        $map['CB Exam Torn'] = $cbYes;
        $map['Exam Torn text'] = substr($claim->examTornText(), 0, 50);
    }

    $val = $claim->examVision();
    if ($val && $val == $Y) {
        $map['CB Exam Vision'] = $cbYes;
        $map['Exam Vision text'] = substr($claim->examVisionText(), 0, 50);
    }

    $val = $claim->examTypeOther();
    if ($val && $val == $Y) {
        $map['CB Exam Type Other'] = $cbYes;
        $map['Exam Type Other text1'] = substr($claim->examTypeOtherText(), 0, 100);
        $map['Exam Type Other text2'] = substr($claim->examTypeOtherText(), 100, 120);
    }

    //  Page 3
    //  4. Physical Examination

    if (($val = $claim->objNone()) && ($val == $Y))
        $map['CB Obj None'] = $cbYes;
    else {
        $val = $claim->objBruise();
        if ($val && $val == $Y) {
            $map['CB Obj Bruise'] = $cbYes;
            $map['Obj Bruise text'] = substr($claim->objBruiseText(), 0, 50);
        }

        $val = $claim->objBurns();
        if ($val && $val == $Y) {
            $map['CB Obj Burns'] = $cbYes;
            $map['Obj Burns text'] = substr($claim->objBurnsText(), 0, 50);
        }

        $val = $claim->objCrepit();
        if ($val && $val == $Y) {
            $map['CB Obj Crepit'] = $cbYes;
            $map['Obj Crepit text'] = substr($claim->objCrepitText(), 0, 50);
        }

        $val = $claim->objDeform();
        if ($val && $val == $Y) {
            $map['CB Obj Deform'] = $cbYes;
            $map['Obj Deform text'] = substr($claim->objDeformText(), 0, 50);
        }

        $val = $claim->objEdema();
        if ($val && $val == $Y) {
            $map['CB Obj Edema'] = $cbYes;
            $map['Obj Edema text'] = substr($claim->objEdemaText(), 0, 50);
        }

        $val = $claim->objHematoma();
        if ($val && $val == $Y) {
            $map['CB Obj Hematoma'] = $cbYes;
            $map['Obj Hematoma text'] = substr($claim->objHematomaText(), 0, 30);
        }

        $val = $claim->objJoint();
        if ($val && $val == $Y) {
            $map['CB Obj Joint'] = $cbYes;
            $map['Obj Joint text'] = substr($claim->objJointText(), 0, 50);
        }

        $val = $claim->objLacerat();
        if ($val && $val == $Y) {
            $map['CB Obj Lacerat'] = $cbYes;
            $map['Obj Lacerat text'] = substr($claim->objLaceratText(), 0, 40);
        }

        $val = $claim->objPain();
        if ($val && $val == $Y) {
            $map['CB Obj Pain'] = $cbYes;
            $map['Obj Pain text'] = substr($claim->objPainText(), 0, 40);
        }

        $val = $claim->objScar();
        if ($val && $val == $Y) {
            $map['CB Obj Scar'] = $cbYes;
            $map['Obj Scar text'] = substr($claim->objScarText(), 0, 50);
        }

        $val = $claim->objOther();
        if ($val && $val == $Y) {
            $map['CB Obj Other'] = $cbYes;
            $map['Obj Other text'] = substr($claim->objOtherText(), 0, 100);
        }

        if (($val = $claim->objNeuro()) && ($val == $Y)) {
            $map['CB Obj Neuro'] = $cbYes;

            if (($val = $claim->objAbnormal()) && ($val == $Y)) {
                $map['CB Obj Abnormal'] = $cbYes;

                $val = $claim->objActive();
                if ($val && $val == $Y) {
                    $map['CB Obj Active'] = $cbYes;
                    $map['Obj Active text'] = substr($claim->objActiveText(), 0, 30);
                }

                $val = $claim->objPassive();
                if ($val && $val == $Y) {
                    $map['CB Obj Passive'] = $cbYes;
                    $map['Obj Passive text'] = substr($claim->objPassiveText(), 0, 30);
                }
            }

            $val = $claim->objGait();
            if ($val && $val == $Y) {
                $map['CB Obj Gait'] = $cbYes;
                $map['Obj Gait text'] = substr($claim->objGaitText(), 0, 50);
            }

            $val = $claim->objPalpable();
            if ($val && $val == $Y) {
                $map['CB Obj Palpable'] = $cbYes;
                $map['Obj Palpable text'] = substr($claim->objPalpableText(), 0, 30);
            }

            $val = $claim->objReflex();
            if ($val && $val == $Y) {
                $map['CB Obj Reflex'] = $cbYes;
                $map['Obj Reflex text'] = substr($claim->objReflexText(), 0, 50);
            }

            $val = $claim->objSensation();
            if ($val && $val == $Y) {
                $map['CB Obj Sensation'] = $cbYes;
                $map['Obj Sensation text'] = substr($claim->objSensationText(), 0, 50);
            }

            $val = $claim->objStrength();
            if ($val && $val == $Y) {
                $map['CB Obj Strength'] = $cbYes;
                $map['Obj Strength text'] = substr($claim->objStrengthText(), 0, 40);
            }

            $val = $claim->objWasting();
            if ($val && $val == $Y) {
                $map['CB Obj Wasting'] = $cbYes;
                $map['Obj Wasting text'] = substr($claim->objWastingText(), 0, 30);
            }
        }
    }

    // 5. - 8.
    if ($val = $claim->visitTests()) {
        $map['Diag Tests1'] = substr($val, 0, 80);
        $map['Diag Tests2'] = substr($val, 80, 140);
    }

    if ($val = $claim->visitTreatments()) {
        $map['Treatments1'] = substr($val, 0, 80);
        $map['Treatments2'] = substr($val, 80, 140);
    }

    if ($val = $claim->visitPrognosis()) {
        $map['Prognosis1'] = substr($val, 0, 100);
        $map['Prognosis2'] = substr($val, 100, 140);
        $map['Prognosis3'] = substr($val, 240, 140);
    }

    if (($val = $claim->injuryPreexist()) && ($val == $Y)) {
        $map['CB Preexist Y'] = $cbYes;
        $map['Preexist text1'] = substr($claim->injuryPreexistText(), 0, 100);
        $map['Preexist text2'] = substr($claim->injuryPreexistText(), 100, 140);
    }
    else
        $map['CB Preexist N'] = $cbYes;


    // G.  Doctor's Opinion
    $val = $claim->conditionCaused();
    $map['CB Caused ' . $val] = $cbYes;

    $val = $claim->conditionComplaints();
    $map['CB Complaints ' . $val] = $cbYes;

    $val = $claim->conditionHistory();
    $map['CB History ' . $val] = $cbYes;

    if ($val = $claim->percentImpaired())
        $map['Percent Impair'] = $val;

    if ($val = $claim->findingsText()) {
        $map['Findings text1'] = substr($val, 0, 120);
        $map['Findings text2'] = substr($val, 120, 140);
    }

    //  H.  Plan of Care
    if ($val = $claim->planTreatmentText()) {
        $map['Plan Treatment text1'] = substr($val, 0, 120);
        $map['Plan Treatment text2'] = substr($val, 120, 140);
        $map['Plan Treatment text3'] = substr($val, 260, 140);
    }

    if ($val = $claim->medsPrescribedText())
        $map['Meds prescribed'] = substr($val, 0, 80);

    if ($val = $claim->medsOTCText())
        $map['Meds OTC'] = substr($val, 0, 70);

    if (($val = $claim->medsRestrictions()) && ($val == $Y)) {
        $map['CB Meds Restrictions Y'] = $cbYes;
        $map['Meds Restrictions text1'] = substr($claim->medsRestrictionsText(), 0, 140);
        $map['Meds Restrictions text2'] = substr($claim->medsRestrictionsText(), 140, 140);
    }
    else
        $map['CB Meds Restrictions N'] = $cbYes;

    // Page 4
    $val = $claim->needTestRefs();
    $map['CB Need Test Refs ' . $val] = $cbYes;

    if ($claim->needTest('scan'))
        $map['CB Need Scan'] = $cbYes;

    if ($claim->needTest('emg'))
        $map['CB Need EMG'] = $cbYes;

    if ($claim->needTest('mri')) {
        $map['CB Need MRI'] = $cbYes;
        $map['Need MRI text'] = substr($claim->needMRIText(), 0, 50);
    }

    if ($claim->needTest('lab')) {
        $map['CB Need Labs'] = $cbYes;
        $map['Need Labs text'] = substr($claim->needLabsText(), 0, 50);
    }

    if ($claim->needTest('xray')) {
        $map['CB Need Xray'] = $cbYes;
        $map['Need Xray text'] = substr($claim->needMRIText(), 0, 50);
    }

    if ($claim->needTest('other')) {
        $map['CB Need Other'] = $cbYes;
        $map['Need Other text'] = substr($claim->needOtherText(), 0, 50);
    }

    if ($claim->needRef('chiro'))
        $map['CB Need Chiro'] = $cbYes;

    if ($claim->needRef('intern'))
        $map['CB Need Internist'] = $cbYes;

     if ($claim->needRef('octher'))
        $map['CB Need OccuTherapy'] = $cbYes;
     
    if ($claim->needRef('phther'))
        $map['CB Need PhysTherapy'] = $cbYes;

    if ($claim->needRef('spec')) {
        $map['CB Need Specialist'] = $cbYes;
        $map['Need Specialist text'] = substr($claim->needSpecialistText(), 0, 50);
    }

    if ($claim->needRef('other')) {
        $map['CB Need Ref Other'] = $cbYes;
        $map['Need Ref Other text'] = substr($claim->needRefOtherText(), 0, 50);
    }

    // 4. Devices
    if ($claim->deviceCane())
        $map['CB Device Cane'] = $cbYes;

    if ($claim->deviceCrutches())
        $map['CB Device Crutch'] = $cbYes;

    if ($claim->deviceOrtho())
        $map['CB Device Ortho'] = $cbYes;

    if ($claim->deviceWalker())
        $map['CB Device Walker'] = $cbYes;

    if ($claim->deviceWheel())
        $map['CB Device Wheel'] = $cbYes;

    if ($claim->deviceOther()) {
        $map['CB Device Other'] = $cbYes;
        $map['Device Other text'] = substr($claim->deviceOtherText(), 0, 110);
    }

    $val = $claim->nextAppointment();
    $map['CB Next ' . $val] = $cbYes;

    if ($claim->adhereGuide() == $Y) {
        $map['CB Guide Y'] = $cbYes;
        $map['Guide Yes text'] = substr($claim->guideYesText(), 0, 80);
    } else {
        $map['CB Guide N'] = $cbYes;
        $map['Guide No text1'] = substr($claim->guideNoText(), 0, 60);
        $map['Guide No text2'] = substr($claim->guideNoText(), 60, 1400);
    }

    //  I. Work Status
    if ($claim->workMissed() == $Y) {
        $map['CB Work Missed Y'] = $cbYes;
        $map['Work Missed date'] = $claim->workMissedDate();
    }
    else
        $map['CB Work Missed N'] = $cbYes;

    if ($claim->workStatus() == $Y) {
        $map['CB Work Status Y'] = $cbYes;
        if ($claim->workCasual() == $Y)
            $map['CB Work Casual'] = $cbYes;
        if ($claim->workLimited() == $Y)
            $map['CB Work Limited'] = $cbYes;

        if ($claim->workRestrictions()) {                       //*** new
            $map['CB Restrictions Y'] = $cbYes;
            if ($val = $claim->restrictionsText())
                $map['Restrictions text'] = substr($val, 0, 140);

            $val = $claim->restrictionsPeriod();                //*** new
            $map['CB Restrictions Period ' . $val] = $cbYes;
        } else
            $map['CB Restrictions N'] = $cbYes;
    } elseif ($claim->workStatus() == $N)
        $map['CB Work Status N'] = $cbYes;

    //   2. Can Patient return to work
    $val = $claim->workReturnOptions();
    if ($val == 'N') {
        $map['CB Return NO'] = $cbYes;
        $map['Return NO text'] = $claim->workReturnText();
    } else if ($val == 'Y') {
        $map['CB Return NO Limits'] = $cbYes;
        if ($val = $claim->workReturnText())
            $map['Return NO Limits Date'] = $val;
    } else if ($val == 'L') {
        $map['CB Limits Return'] = $cbYes;
        $map['Limits Return date'] = $claim->workReturnText();
        if ($claim->limits('bend'))
            $map['CB Limits Bend'] = $cbYes;
        if ($claim->limits('lift'))
            $map['CB Limits Lift'] = $cbYes;
        if ($claim->limits('sit'))
            $map['CB Limits Sitting'] = $cbYes;
        if ($claim->limits('climb'))
            $map['CB Limits Climb'] = $cbYes;
        if ($claim->limits('heavy'))
            $map['CB Limits Heavy'] = $cbYes;
        if ($claim->limits('stand'))
            $map['CB Limits Stand'] = $cbYes;
        if ($claim->limits('env'))
            $map['CB Limits Env'] = $cbYes;
        if ($claim->limits('veh'))
            $map['CB Limits Vehicle'] = $cbYes;
        if ($claim->limits('public'))
            $map['CB Limits Public'] = $cbYes;
        if ($claim->limits('kneel'))
            $map['CB Limits Kneel'] = $cbYes;
        if ($claim->limits('pers'))
            $map['CB Limits Personal'] = $cbYes;
        if ($claim->limits('upper'))
            $map['CB Limits Upper'] = $cbYes;
        if ($claim->limits('other')) {
            $map['CB Limits Other'] = $cbYes;
            $map['Limits Other text'] = substr($claim->limitsOtherText(), 0, 100);
        }
        if ($val = $claim->limitsQuantText()) {
            $map['Limits Quant1'] = substr($val, 0, 90);
            $map['Limits Quant2'] = substr($val, 90, 130);
        }
        $val = $claim->limitsPeriod();
        $map['CB Limit Period ' . $val] = $cbYes;
    }

    // 3. With whom discussed
    $val = $claim->returnWorkDiscuss();
    if ($val == 'P') $t = 'Patient';
    else if ($val == 'E') $t = 'Employer';
         else $t = $val;
    $map['CB Return Discuss ' . $t] = $cbYes;

    $map['CB Services'] = $cbYes;
    $map['Doctor Specialty'] = $claim->doctorSpecialty();

    return $map;
}

function populateNF3($claim, $inStr) {

    setlocale(LC_MONETARY, 'en_US');
    $chkBox = array('X', 1);

    $today = time();
    $map = array();

    // Header
    $map['Name'] = array($claim->patientLastName() . ', ' . $claim->patientFirstName());

    $map['Address'] = array($claim->patientStreet() . ', ' . $claim->patientCity() . ', ' . $claim->patientState() . ' ' . $claim->patientZip());

    $map['InsName'] = array($claim->payerName());

    $map['InsAddress'] = array($claim->payerStreet(0) . ', ' . $claim->payerCity(0) . ', ' . $claim->payerState() . ' ' . $claim->payerZip(0));

    $map['now'] = array(date('m/d/y', $today));

    $map['Insured'] = array($claim->insuredLastName(0) . ', ' . $claim->insuredFirstName(0));

    $map['InsPolicy'] = array($claim->policyNumber(0));

    if ($val = $claim->accidentDate())
        $map['InjDate'] = array(formatDate($val));

    $map['InsClaim'] = array($claim->claimNumber());

    $map['FacilityName'] = array($claim->facilityName());

    $map['FacilityAddress'] = array($claim->facilityStreet() . ', ' . $claim->facilityCity() . ', ' . $claim->facilityState() . ' ' . $claim->facilityZip());


    // Section 2
    $val = $claim->patientDOB();
    $map['dob'] = array(formatDate($val));

    $map['gender'] = array($claim->patientSex());

    $i = 1;
    foreach ($claim->diagTexts as $cde => $txt) {
        $map['icdCT' . $i] = array($cde . ' - ' . $txt);
        $i++;
    }

    // 6.
    if ($val = $claim->firstSymptomDate())
        $map['symptom1'] = array(formatDate($val));

    // 7.
    if ($val = $claim->firstVisitDate())
        $map['visit1'] = array(formatDate($val));

    // 8.
    if ($val = $claim->priorCondition())
        $map['same' . $val] = $chkBox;
    if ($val && ($val == 'Y') && ($t = $claim->priorText()))
        $map['sameText'] = array($t);

    // 9.
    if ($val = $claim->solelyAuto())
        $map['solel' . $val] = $chkBox;
    if ($val && ($val == 'N') && ($t = $claim->solelyText()))
        $map['solelyText'] = array($t);

    // 10.
    if ($val = $claim->employmentRelated())
        $map['frEmpl' . $val] = $chkBox;

    // 11.
    if ($val = $claim->permanentDisability())
        $map['disab' . $val] = $chkBox;
    if ($val && ($val == 'Y') && ($t = $claim->disabilityText()))
        $map['disabilityText'] = array($t);

    // 12.
    if ($val = $claim->disabilityFrom())
        $map['disabFr'] = array(formatDate($val));

    if ($val = $claim->disabilityTo())
        $map['disabTo'] = array(formatDate($val));

    // 13.
    if ($val = $claim->returnToWork())
        $map['WrDate'] = array(formatDate($val));

    // 14.
    if ($val = $claim->needRehab())
        $map['rehab' . $val] = $chkBox;

    if ($val && ($val == 'Y') && ($t = $claim->rehabText()))
        $map['rehabText'] = array($t);

    $val = $claim->serviceDate();
    $map['Cdate'] = array(formatDate($val));
    $map['dAddrZ'] = array($claim->facilityZip(), 10);
    $totalCh = 0;
    for ($i = 0; $i < $claim->procCount(); $i++) {
        $map['Cpt' . ($i + 1)] = array($claim->procs[$i]['code']);
        $map['CptD' . ($i + 1)] = array($claim->procs[$i]['code_text'], 40);
        $val = $claim->procs[$i]['fee'];
        $totalCh += $val;
        $map['Cch' . ($i + 1)] = array(money_format('%.2n', $val));
    }
    $map['CchT'] = array(money_format('%.2n', $totalCh));

    $map['dName'] = array($claim->providerFirstName() . ', ' . $claim->providerLastName());
    $map['dTtl'] = array($claim->doctorTitle());
    $map['WCBAuth'] = array($claim->WCBAuth());

    $map['FacEIN'] = array($claim->facilityETIN());
    $map['WCBRat'] = array($claim->WCBRating());

    $outStr = transform($inStr, $map);
    return $outStr;
}

?>

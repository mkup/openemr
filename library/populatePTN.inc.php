<?php

// Copyright (C) 2012-2016 Mark Kuperman <mkuperman@mi-10.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2

function populatePTN($claim) {
    $cbYes = 'Yes';
    $Y = 'Y';
    $map = array();

    $map['Facility Name'] = $claim->facilityName();
    $map['Facility Address1'] = $claim->facilityStreet();
    $map['Facility Address2'] = $claim->facilityCity() . ', ' . $claim->facilityState() . '  ' . $claim->facilityZip();
    $map['Facility Phone'] = $claim->facilityPhone();
    
    $map['Patient Name'] = $claim->patientLastName() . ', ' . $claim->patientFirstName();
    $map['Service Date'] = formatDate($claim->serviceDate());
    
    foreach ($claim->getDiagnosis() as $k=>$v) 
        $map['CB Diagnosis ' . $k] = $cbYes;
    
    $map['Diagnosis Other text'] = substr($claim->diagOtherText(), 0, 100);
    
    $val = $claim->subjectiveText();
    $map['Subjective Text1'] = substr($val, 0, 120);
    $map['Subjective Text2'] = substr($val, 120, 120);

    foreach ($claim->getObjective() as $k=>$v) 
        $map['CB Objective ' . $k] = $cbYes;
    
    $map['Objective Other text'] = substr($claim->objOtherText(), 0, 100);
    
    $val = $claim->assessmentText();
    $map['Assessment Text1'] = substr($val, 0, 120);
    $map['Assessment Text2'] = substr($val, 120, 120);

    $val = $claim->planText();
    $map['Plan Text1'] = substr($val, 0, 120);
    $map['Plan Text2'] = substr($val, 120, 120);

    $map['Physician Name'] = $claim->therapistFirstName() . ' ' . $claim->therapistLastName();
    $map['Physician Specialty'] = $claim->therapistSpecialty();
    
    return $map;
}
?>

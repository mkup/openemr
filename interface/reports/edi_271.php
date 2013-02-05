<?php
// Copyright (C) 2010 MMF Systems, Inc>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
<<<<<<< HEAD

        //SANITIZE ALL ESCAPES
        $sanitize_all_escapes=true;
        //

        //STOP FAKE REGISTER GLOBALS
        $fake_register_globals=false;
        //

	//	START - INCLUDE STATEMENTS
	include_once(dirname(__file__)."/../globals.php");
	include_once("$srcdir/forms.inc");
	include_once("$srcdir/billing.inc");
	include_once("$srcdir/patient.inc");
	include_once("$srcdir/report.inc");
	include_once("$srcdir/calendar.inc");
	include_once("$srcdir/classes/Document.class.php");
	include_once("$srcdir/classes/Note.class.php");
	include_once("$srcdir/sqlconf.php");
	include_once("$srcdir/edi.inc");

	// END - INCLUDE STATEMENTS 


	//  File location (URL or server path) 
	
	$target			= $GLOBALS['edi_271_file_path']; 

	if(isset($_FILES) && !empty($_FILES))
	{

			$target		= $target .time().basename( $_FILES['uploaded']['name']);
	
			$FilePath	= $target;
			
			if ($_FILES['uploaded']['size'] > 350000) 
			{ 
				$message .= htmlspecialchars( xl('Your file is too large'), ENT_NOQUOTES)."<br>";
				
			}
			
			if ($_FILES['uploaded']['type']!="text/plain") 
			{ 
				 $message .= htmlspecialchars( xl('You may only upload .txt files'), ENT_NOQUOTES)."<br>"; 				 
			} 
			if(!isset($message))
			{
				if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) 
				{ 
					$message	= htmlspecialchars( xl('The following EDI file has been uploaded').': "'. basename( $_FILES['uploaded']['name']).'"', ENT_NOQUOTES); 
					
					// Stores the content of the file    
					$Response271= file($FilePath);

					// Counts the number of lines       
					$LineCount	= count($Lines);
					
					//This will be a two dimensional array 
					//that holds the content nicely organized 

					$DataSegment271 = array();
					$Segments271	= array();
					
					// We will use this as an index 
					$i			=	0;
					$j			=	0;
					$patientId	= "";

					// Loop through each line 
					foreach($Response271 as $Value)
					{
					   // In the array store this line 
						// with values delimited by ^ (tilt) 
						// as separate array values 
						
						$DataSegment271[$i] = explode("^", $Value);
						
						
						if(count($DataSegment271[$i])<6)
						{
								$messageEDI	= true;
								$message = "";
								if(file_exists($target))
								{
									unlink($target);
								}
						}
						else
						{
							foreach ($DataSegment271[$i] as $datastrings)
							{
								
								$Segments271[$j] = explode("*", $datastrings);
								
								$segment		 = $Segments271[$j][0];

								
								// Switch Case for Segment
								
								switch ($segment) 
								{
									case 'ISA':
										
										$j = 0;
									
										foreach($Segments271[$j] as $segmentVal){
											
											if($j == 6)
											{
												$x12PartnerId = $segmentVal;
											}
											
											$j	=	$j + 1;
										}
										
										break;

									case 'REF':

										foreach($Segments271[$j] as $segmentVal){
											
											if($segmentVal == "EJ")
											{
												$patientId = $Segments271[$j][2];
											}
										}
										
										break;

									case 'EB':

										foreach($Segments271[$j] as $segmentVal){
											
											
										}
										break;

									case 'MSG':
										
										foreach($Segments271[$j] as $segmentVal){
							
											if($segment != $segmentVal)
											{
												eligibility_response_save($segmentVal,$x12PartnerId);
												
												eligibility_verification_save($segmentVal,$x12PartnerId,$patientId);
											}
										}
										
										break;


							
								}

								
							   
							   // Increase the line index 
							   $j++;
							}
						}
					  //Increase the line index  
					   $i++;
					}
				}				
			} 
			else 
			{ 
				$message .= htmlspecialchars( xl('Sorry, there was a problem uploading your file'), ENT_NOQUOTES). "<br><br>"; 
			}  
	}
	
?>
<html>
<head>
<?php html_header_show();?>
<title><?php echo htmlspecialchars( xl('EDI-271 Response File Upload'), ENT_NOQUOTES); ?></title>
<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<style type="text/css">

/* specifically include & exclude from printing */
@media print {
    #report_parameters {
        visibility: hidden;
        display: none;
    }
    #report_parameters_daterange {
        visibility: visible;
        display: inline;
    }
    #report_results table {
       margin-top: 0px;
=======
//SANITIZE ALL ESCAPES
$sanitize_all_escapes = true;
//
//STOP FAKE REGISTER GLOBALS
$fake_register_globals = false;
//
//	START - INCLUDE STATEMENTS
include_once(dirname(__file__) . "/../globals.php");
include_once("$srcdir/forms.inc");
include_once("$srcdir/billing.inc");
include_once("$srcdir/pnotes.inc");
include_once("$srcdir/patient.inc");
include_once("$srcdir/report.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/classes/Document.class.php");
include_once("$srcdir/classes/Note.class.php");
include_once("$srcdir/sqlconf.php");
include_once("$srcdir/edi.inc");

// END - INCLUDE STATEMENTS
$segSeparator = "~";
$statusY = "Active";
$statusN = "Inactive";
$statusU = "Undetermined";
$dtNow = date('Ymd');

//  File location (URL or server path)

$target = $GLOBALS['edi_271_file_path'];

if (isset($_FILES) && !empty($_FILES)) {

    $target = $target . time() . basename($_FILES['uploaded']['name']);

    $FilePath = $target;

    if ($_FILES['uploaded']['size'] > 350000) {
        $message .= htmlspecialchars(xl('Your file is too large'), ENT_NOQUOTES) . "<br>";
>>>>>>> initial prod changes
    }

    if ($_FILES['uploaded']['type'] != "text/plain") {
        $message .= htmlspecialchars(xl('You may only upload .txt files'), ENT_NOQUOTES) . "<br>";
    }
    if (!isset($message)) {
        if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
            $message = htmlspecialchars(xl('The following EDI file has been uploaded') . ': "' . basename($_FILES['uploaded']['name']) . '"', ENT_NOQUOTES);

            // Stores the content of the file
            $Response271 = file($FilePath);

            // Counts the number of lines
            $LineCount = count($Response271);          //bug
            //This will be a two dimensional array
            //that holds the content nicely organized

            $Segments271 = array();
            $rpt = array();

            // Loop through each line (this handles 1-line file too)
            foreach ($Response271 as $lineValue) {
                // parse all the segments in to $Segments271 array
                // assumption: line contains complete segments

                $Segments271 = explode($segSeparator, $lineValue);


                // i guess this is "wrong file" validation
                if (count($Segments271) < 6) {
                    $messageEDI = true;
                    $message = "";
                    if (file_exists($target)) {
                        unlink($target);
                    }
                } else {
                    foreach ($Segments271 as $segData) {
                        $segValues = explode("*", $segData);
                        $segId = $segValues[0];

                        // Switch Case for Segment
                        switch ($segId) {
                            case 'ISA':
                                $x12PartnerId = $segValues[6];
                                break;

                            case 'ST':
                                $patient_id = "";
                                $nm1Seg = "";
                                $msgArray = array();
                                $msgTxt = "";
                                $status = $statusY;
                                $cop = "";
                                $ded = "";
                                $dedRemain = "";
                                $rptLine = array();
                                $fromDt = "20000101";
                                $toDt = "99991231";
                                break;

                            case 'NM1':
                                if ($segValues[1] == "PR") {
                                    $rptLine['insurance'] = $segValues[3];
                                } else
                                if ($segValues[1] == "IL" or $segValues[1] == "03") {
                                    //NM1 for subscriber or dependent
                                    $nm1Seg = $segValues;
                                    $rptLine['fName'] = $segValues[4];
                                    $rptLine['lName'] = $segValues[3];
                                    $rptLine['policy'] = $segValues[9];
                                }
                                break;

                            case 'AAA':
                                $aaa1 = $segValues[1];
                                $aaa3 = $segValues[3];
                                if ($aaa1 == "N" && $aaa3 == "67") {
                                    $status = $statusU;
                                    $msgTxt = "N67:Invalid Request. Reason Unknown";
                                } else
                                if ($aaa1 == "N" && $aaa3 == "79") {
                                    $status = $statusU;
                                    $msgTxt = "N79:Invalid Request. Check Insurance Payor ID";
                                } else
                                if ($aaa1 == "Y" && $aaa3 == "67") {
                                    $status = $statusU;
                                    $msgTxt = "Y67:Patient Not Found";
                                } else
                                if ($aaa1 == "Y" && $aaa3 == "71") {
                                    $status = $statusU;
                                    $msgTxt = "Y71:Invalid Subscriber DOB";
                                } else
                                if ($aaa1 == "Y" && $aaa3 == "73") {
                                    $status = $statusU;
                                    $msgTxt = "Y73:Invalid Subscriber Name";
                                } else
                                if ($aaa1 == "Y" && $aaa3 == "75") {
                                    $status = $statusN;
                                    $msgTxt = "Y75:Subscriber not found";
                                } else {
                                    $status = $statusU;
                                    $msgTxt = "Y--:Invalid Request";
                                }

                                break;

                            case 'DTP':
                                if (($segValues[1] == "307" OR $segValues[1] == "291") && $segValues[2] == 'RD8') {
                                    $dts = explode('-', $segValues[3]);
                                    $fromDt = $dts[0];
                                    $toDt = $dts[1];
                                } else
                                if ($segValues[1] == "346" OR $segValues[1] == "356") {
                                    $fromDt = $segValues[3];
                                } else
                                if ($segValues[1] == "347" OR $segValues[1] == "357" ) {
                                    $toDt = $segValues[3];
                                }
                                break;

                            case 'REF':
                                if ($segValues[1] == "EJ") {
                                    $patient_id = $segValues[2];
                                }
                                break;

                            case 'EB':
                                $eb1 = $segValues[1];
                                switch ($eb1) {
                                    case 'V':
                                        $status = $statusU;
                                        break;
                                    case 'B':
                                        if ($segValues[3] == '98') {
                                            // Co-pays
                                            $cop = $segValues[7];
                                            $rptLine['copay'] = $cop;
                                        }
                                        break;
                                    case 'C':
                                        if ($segValues[6] == '23') {
                                            //yearly deductible
                                            $ded = $segValues[7];
                                            $rptLine['deductible'] = $ded;
                                        } else {
                                            if ($segValues[6] == '29'){
                                                //remaining deductible
                                                $dedRemain = $segValues[7];
                                            }
                                        }
                                        break;
                                }
                                break;

                            case 'MSG':
                                array_push($msgArray, $segValues[1]);
                                break;

                            case 'SE':
                                //end of loops for a patient
                                if ($fromDt > $dtNow OR $toDt < $dtNow OR $fromDt == $toDt) {
                                    $status = $statusN;
                                    $msgTxt = "Eligibility Dates: " . $fromDt . "-" . $toDt . $msgTxt;
                                }
                                $msgTxt .= "\n" . $msgArray[0] . $msgArray[1];
                                eligibility_response_save($status, $msgTxt, $x12PartnerId);

                                eligibility_verification_save($msgTxt,$x12PartnerId,$patient_id,$nm1Seg,$cop,$ded,$dedReamain);
                                $rptLine['status'] = $status;
                                $rptLine['msg'] = $msgTxt;
                                array_push($rpt, $rptLine);
                                break;
                        }
                    }
                }
            }
        }
    } else {
        $message .= htmlspecialchars(xl('Sorry, there was a problem uploading your file'), ENT_NOQUOTES) . "<br><br>";
    }
}
?>
<html>
    <head>
        <?php html_header_show(); ?>
        <title><?php echo htmlspecialchars(xl('EDI-271 Response File Upload'), ENT_NOQUOTES); ?></title>
        <link rel=stylesheet href="<?php echo $css_header; ?>" type="text/css">
        <style type="text/css">

            /* specifically include & exclude from printing */
            @media print {
                #report_parameters {
                    visibility: hidden;
                    display: none;
                }
                #report_parameters_daterange {
                    visibility: visible;
                    display: inline;
                }
                #report_results table {
                    margin-top: 0px;
                }
            }

            /* specifically exclude some from the screen */
            @media screen {
                #report_parameters_daterange {
                    visibility: hidden;
                    display: none;
                }
            }

        </style>

        <script type="text/javascript" src="../../library/textformat.js"></script>
        <script type="text/javascript" src="../../library/dialog.js"></script>
        <script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>

        <script type="text/javascript">
            function edivalidation(){
			
                var mypcc = "<?php echo htmlspecialchars(xl('Required Field Missing: Please choose the EDI-271 file to upload'), ENT_QUOTES); ?>";

                if(document.getElementById('uploaded').value == ""){
                    alert(mypcc);
                    return false;
                }
                else
                {
                    $("#theform").submit();
                }
			
            }
        </script>

    </head>
    <body class="body_top">

        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <?php
        if (isset($message) && !empty($message)) {
        ?>
            <div style="margin-left:25%;width:50%;color:RED;text-align:center;font-family:arial;font-size:15px;background:#ECECEC;border:1px solid;" ><?php echo $message; ?></div>
        <?php
            $message = "";
        }
        if (isset($messageEDI)) {
        ?>
            <div style="margin-left:25%;width:50%;color:RED;text-align:center;font-family:arial;font-size:15px;background:#ECECEC;border:1px solid;" >
            <?php echo htmlspecialchars(xl('Please choose the proper formatted EDI-271 file'), ENT_NOQUOTES); ?>
        </div>
        <?php
            $messageEDI = "";
        }
        ?>

        <div>

            <span class='title'><?php echo htmlspecialchars(xl('EDI-271 File Upload'), ENT_NOQUOTES); ?></span>

            <form enctype="multipart/form-data" name="theform" id="theform" action="edi_271.php" method="POST" onsubmit="return top.restoreSession()">

                <div id="report_parameters">
                    <table>
                        <tr>
                            <td width='550px'>
                                <div style='float:left'>
                                    <table class='text'>
                                        <tr>
                                            <td style='width:125px;' class='label'> <?php echo htmlspecialchars(xl('Select EDI-271 file'), ENT_NOQUOTES); ?>:	</td>
                                            <td> <input name="uploaded" id="uploaded" type="file" size=37 /></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td align='left' valign='middle' height="100%">
                                <table style='border-left:1px solid; width:100%; height:100%' >
                                    <tr>
                                        <td>
                                            <div style='margin-left:15px'>
                                                <a href='#' class='css_button' onclick='return edivalidation(); '><span><?php echo htmlspecialchars(xl('Upload'), ENT_NOQUOTES); ?></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>


                <input type="hidden" name="form_orderby" value="<?php echo htmlspecialchars($form_orderby, ENT_QUOTES); ?>" />
                <input type='hidden' name='form_refresh' id='form_refresh' value=''/>

            </form>
        </div>
	<?php
            if (isset($rpt) && count($rpt) > 0) {
              show_271_results($rpt);
              }
        ?>
    </body>
</html>

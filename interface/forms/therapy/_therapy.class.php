<?php

/**
 * Description of _therapy
 *
 * @author mark.kuperman@mi-10.com
 */
class _therapy {

    var $col;
    var $row;
    var $formId;
    var $record;
    var $facility;
    var $total = 0;

    function _therapy($id = 0) {
        $this->formId = $id;
        //populate matrix arrays
        $this->col = array(
            'title' => array('label' => 'Modality', 'total' => 0),
            'hmpc' => array('label' => 'HMP/ CP', 'total' => 0),
            'tens' => array('label' => 'TENS/ES', 'total' => 0),
            'ultr' => array('label' => 'Ultrasound', 'total' => 0),
            'para' => array('label' => 'Paraffinwax Bath', 'total' => 0),
            'thma' => array('label' => 'Therapeutic Massage', 'total' => 0),
            'thex' => array('label' => 'Theraputic Exercise', 'total' => 0),
            'trac' => array('label' => 'Traction', 'total' => 0),
            'myfr' => array('label' => 'Myofacial Release (MFR)', 'total' => 0),
            'mobi' => array('label' => 'Mobilization (MOB)', 'total' => 0),
            'neur' => array('label' => 'Neuro-muscular Re-education (Nuer R)', 'total' => 0),
            'gait' => array('label' => 'Gait Training', 'total' => 0),
            'home' => array('label' => 'Home Exercise Program (H.E.P.)', 'total' => 0),
            'othr' => array('label' => 'Other', 'total' => 0)
        );
        $this->row = array(
            'title' => array('label' => 'Diagnosis', 'skip' => 0),
            'cerv'  => array('label' => 'Cervical (neck)', 'skip' => 1),
            'thor'  => array('label' => 'Thoraic (middle back)', 'skip' => 1),
            'lmbr'  => array('label' => 'Lumbar (lower back)', 'skip' => 1),
            'shdl'  => array('label' => 'Lt Shoulder', 'skip' => 1),
            'shdr'  => array('label' => 'Rt Shoulder', 'skip' => 1),
            'arml'  => array('label' => 'Lt Arm', 'skip' => 1),
            'armr'  => array('label' => 'Rt Arm', 'skip' => 1),
            'elbl'  => array('label' => 'Lt Elbow', 'skip' => 1),
            'elbr'  => array('label' => 'Rt Elbow', 'skip' => 1),
            'wrsl'  => array('label' => 'Lt Wrist', 'skip' => 1),
            'wrsr'  => array('label' => 'Rt Wrist', 'skip' => 1),
            'hndl'  => array('label' => 'Lt Hand', 'skip' => 1),
            'hndr'  => array('label' => 'Rt Hand', 'skip' => 1),
            'hipl'  => array('label' => 'Lt Hip', 'skip' => 1),
            'hipr'  => array('label' => 'Rt Hip', 'skip' => 1),
            'knel'  => array('label' => 'Lt Knee', 'skip' => 1),
            'kner'  => array('label' => 'Rt Knee', 'skip' => 1),
            'ankl'  => array('label' => 'Lt Ankle', 'skip' => 1),
            'anlr'  => array('label' => 'Rt Ankle', 'skip' => 1),
            'fool'  => array('label' => 'Lt Foot', 'skip' => 1),
            'foor'  => array('label' => 'Rt Foot', 'skip' => 1),
            'othr'  => array('label' => 'Other', 'skip' => 1),
            'total' => array('label' => 'Total', 'skip' => 0)
            );
        $this->facility = sqlQuery("SELECT * FROM users WHERE specialty = 'therapy' LIMIT 1");
        
        if ($this->formId) {
            //get row of the form table
            $this->record = sqlQuery("SELECT * FROM form_therapy where id = ?", array($this->formId));
            if (empty($this->record)) {
                $this->formId = 0;
                $this->record = null;
            } else {
                $this->unmarshal();
            }
        }
    }

    function matrix($mode = 'input') {
        $s = $this->style();
        if ($mode == 'report') 
            $s .= $this->printLink();
        $s .= " <div class='matrix' >" .
                "<table width='100%' cellpadding='0' cellspacing='0'>";

        foreach ($this->row as $rk => $rv) {
            if (($mode != 'input') AND ($this->skipRow($rk)))
                continue;
            if ($rk == 'title') {
                $s .= "<thead>";
            } 
            if (($rk == 'total') AND ($mode == 'input'))
                continue;
            $s .= "<tr>";
            $val = '';
            $fill = '';

            foreach ($this->col as $ck => $cv) {
                if ($ck == 'title') {
                    $algn = "style='text-align:left;'";
                    $fld = '';
                    $val = $rv['label'];
                    $fill = $val . ' / ';
                }
                if ($rk == 'title') {
                    $val = $fill . $cv['label'];
                    $fill = '';
                }
                
                if (($ck != 'title') AND ($rk != 'title')) {
                    $algn = '';
                    if ($this->formId)
                        $val = $this->valueAt($rk, $ck);
                    else 
                        $val = '';
                    
                    if ($mode == 'input') {
                        $t = 'MTRX-' . $rk . '-' . $ck;    //input field name
                        $fld = "<input type='text' name='" . $t . "' id='" . $t . "' size='5' maxlength='5' title='Time in minutes' value='";
                        $val .= "'>";
                    } else {
                        $fld = '';
                    }
                }
                $s .= "<td " . $algn . ">" . $fld . $val . "</td>";
            }
            $s .= "</tr>";
            if ($rk == 'title')
                $s .= "</thead>" .
                        "<tbody>";
        }
        $s .= "</tbody>" .
                "</table>" .
                "</div>";
        return $s;
    }

    function marshal($ar) {
        $s = '';
        $d = '';
        foreach ($ar as $n => $v) {
            if ((substr($n, 0, 4) == 'MTRX') AND !empty($v)) {
                $s .= $d . substr($n, 5) . '-' . $v;
                $d = ';';
            }
        }
        return $s;
    }

    function unmarshal() {
        //parse the -combination- field of the form table, populate row array with numbers
        $sets = explode(';', $this->record['combination']);
        foreach ($sets as $set) {
            $t = explode('-', $set);
            $this->col[$t[1]][$t[0]] = $t[2];
            $this->row[$t[0]]['skip'] = 0;
            $this->col[$t[1]]['total'] += $t[2];
            $this->total += $t[2];
        }
    }

    function valueAt($r, $c) {
        return empty($this->col[$c][$r]) ? '' : $this->col[$c][$r];
    }

    function style() {
        return "<style>
.matrix tr.head   {font-size:8pt; }
.matrix tr.detail { font-size:8pt; }

.matrix table {
 border-style: solid;
 border-width: 1px 1px 1px 1px; 
 border-color: black;
 }

.matrix td, .matrix th {
 font-size: 8pt;
 border-style: solid;
 border-width: thin thin thin thin;
 border-color: black;
 text-align:center;
}

</style>";
    }
    
    function printLink() {
        $f = htmlspecialchars("'" . $GLOBALS['rootdir'] . "/forms/therapy/print.php?id=" . $this->formId . "'", ENT_QUOTES);
        return "<a href='javascript:;' onclick='window.open(" . $f . ")'><i>Print Form</i></a>";
    }
    
    function addressHeader() {
        return $this->facility['organization'] . 
                "<br />" . $this->facility['street'] . 
                "<br />" . $this->facility['city'] . '&nbsp;' .
                           $this->facility['state'] . '&nbsp;' . 
                           $this->facility['zip'] . 
                "<br />t." . $this->facility['phonew1'] . 
                "<br />";
    }
    
    function getDiag() {
        return $this->record['diag'];
    }
    
    function getNotes() {
        return $this->record['notes'];
    }
    
    function skipRow($k) {
        return ($this->row[$k]['skip'] == 1);
    }
    
    function getTotal() {
        return $this->total;
    }
}

// end of class declaration
?>
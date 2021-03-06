<?

	class HMDISEPWM55 extends IPSModule {

	public function Create() {

		parent::Create();

		$this->RegisterPropertyString("ipadress", "");
		$this->RegisterPropertyString("serialnumber", "NEQXXXXXXX");

	}

	public function ApplyChanges() {

		parent::ApplyChanges();

		if (@IPS_GetInstanceIDByName("Taste unten", $this->InstanceID) == false) {
			$InsID = IPS_CreateInstance("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
			IPS_SetName($InsID, "Taste unten");
			IPS_SetParent($InsID, $this->InstanceID);
		}
		IPS_SetConfiguration(@IPS_GetInstanceIDByName("Taste unten", $this->InstanceID), '{"Protocol":0,"Address":"'.$this->ReadPropertyString("serialnumber").':1","EmulateStatus":true}');
		@IPS_ApplyChanges(@IPS_GetInstanceIDByName("Taste unten", $this->InstanceID));

		if (@IPS_GetInstanceIDByName("Taste oben", $this->InstanceID) == false) {
			$InsID = IPS_CreateInstance("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
			IPS_SetName($InsID, "Taste oben");
			IPS_SetParent($InsID, $this->InstanceID);
		}
		IPS_SetConfiguration(@IPS_GetInstanceIDByName("Taste oben", $this->InstanceID), '{"Protocol":0,"Address":"'.$this->ReadPropertyString("serialnumber").':2","EmulateStatus":true}');
		@IPS_ApplyChanges(@IPS_GetInstanceIDByName("Taste oben", $this->InstanceID));

		if (($this->ReadPropertyString("ipadress") != "") and ($this->ReadPropertyString("serialnumber") != ""))
			{
				$this->SetStatus(102);
			} else {
				$this->SetStatus(202);
			}

	}

	public function umsetzen($display_line) {

		$string = "0x02,0x0A";
		if ($display_line[1]['text'] != "") {
            $string .= ",0x12,".HMDIS_hex_encode($this->InstanceID, $display_line[1]['text']);
            if ($display_line[1]['icon'] != ""){
                $string .= "0x13,".$display_line[1]['icon'].",0x0A";
            } else {
               $string .= "0x0A";
            }
        } else {
            $string.= ",0x0A";
        }
        if ($display_line[2]['text'] != "") {
            $string .= ",0x12,".HMDIS_hex_encode($this->InstanceID, $display_line[2]['text']);
            if ($display_line[2]['icon'] != ""){
                $string .= "0x13,".$display_line[2]['icon'].",0x0A";
            } else {
               $string .= "0x0A";
            }
        } else {
            $string.= ",0x0A";
        }
        if ($display_line[3]['text'] != "") {
            $string .= ",0x12,".HMDIS_hex_encode($this->InstanceID, $display_line[3]['text']);
            if ($display_line[3]['icon'] != ""){
                $string .= "0x13,".$display_line[3]['icon'].",0x0A";
            } else {
               $string .= "0x0A";
            }
        } else {
            $string.= ",0x0A";
        }
    return $string;
}


public function hex_encode($ascii) {
    if (strlen($ascii) > 0 and strlen($ascii) < 5 and strlen($ascii) == 4){

        if ($ascii{0} == "0" and $ascii{1} == "x"){
          $hex = $ascii;
          return $hex.",";
          }
    }
    $hex = '';
    for ($i = 0; $i < strlen($ascii) and $i < 12; $i++) {
        $byte = strtoupper(dechex(ord($ascii{$i})));
        $byte = str_repeat('0', 2 - strlen($byte)).$byte;
        $hex.="0x".$byte.",";
        }
    return $hex;
}


public function HMRS_HTTP_Post($CCU_IP, $HM_Script) {
$fp = fsockopen ($CCU_IP, 8181, $errno, $errstr, 2);
$res = "";
    if (!$fp) {
        $res = "$errstr ($errno)<br />\n";
    } else {
        fputs($fp, "POST /Test.exe HTTP/1.1\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($HM_Script) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $HM_Script);
        while(!feof($fp)) {
            $res .= fgets($fp, 500);
        }
        fclose($fp);
    }
    return $res;
}

public function wiederholungen_abstand($wiederholungen, $string, $abstand) {
if ($wiederholungen < 1){
       $string = $string . "0xDF,0x1D,";
}   else {
       if ($wiederholungen < 11) {
          $string = $string . "0xD" . ($wiederholungen - 1) . ",0x1D,";
       }
       else {
          if ($wiederholungen == 11) {
             $string = $string . "0xDA,0x1D,";
          }
          else {
             if ($wiederholungen == 12) {
                $string = $string . "0xDB,0x1D,";
             }
             else {
                if ($wiederholungen == 13) {
                   $string = $string . "0xDC,0x1D,";
                }
                else {
                   if ($wiederholungen == 14) {
                      $string = $string . "0xDD,0x1D,";
                   }
                   else {
                      $string = $string . "0xDE,0x1D,";
                   }
                }
             }
          }
       }
    }


if ($abstand <= 10) {
       $string = $string . "0xE0,0x16,";
}   else {
       if ($abstand <= 100) {
          $string = $string . "0xE" . ($abstand - 1 / 10) . ",0x16,";
       }
       else {
          if ($abstand <= 110) {
             $string = $string . "0xEA,0x16,";
          }
          else {
             if ($abstand <= 120) {
                $string = $string . "0xEB,0x16,";
             }
             else {
                if ($abstand <= 130) {
                   $string = $string . "0xEC,0x16,";
                }
                else {
                   if ($abstand <= 140) {
                      $string = $string . "0xED,0x16,";
                   }
                   else {
                      if ($abstand <= 150) {
                         $string = $string . "0xEE,0x16,";
                      }
                      else {
                         $string = $string . "0xEF,0x16,";
                      }
                   }
                }
             }
          }
       }
    }
return $string;
}



	// Lese alle Konfigurationsdaten aus und schreibe sie in Variablen
	public function writeDisplay($zeile1, $zeile2, $zeile3, $icon1, $icon2, $icon3, $signal, $tonfolge ) {

		$CCU_IP = $this->ReadPropertyString("ipadress");  // IP der CCU2
		$Seriennummer = $this->ReadPropertyString("serialnumber"); // Seriennummer des Display

		//--------------- $signal ------------------------
		// Definition der Werte für die Signale
		//
		//  ! 0xF0 AUS
		//  ! 0xF1 Rotes Blitzen
		//  ! 0xF2 Grünes Blitzen
		//  ! 0xF3 Orangenes Blitzen

		//----------------- $tonfolge ----------------------
		// Definition der Werte für die Tonfolgen
		//
		//  ! 0xC0 AUS
		//  ! 0xC1 LANG LANG
		//  ! 0xC2 LANG KURZ
		//  ! 0xC3 LANG KURZ KURZ
		//  ! 0xC4 KURZ
		//  ! 0xC5 KURZ KURZ
		//  ! 0xC6 LANG

		// Wiederholungen
		// 1 bis 15. 0 = Unendlich
		$wiederholungen = 1;

		// es wird zum nächstmöglichen Abstand aufgerundet. Maximum ist 160s.
		$abstand = 10;

		//---------------------------------------
		//    ******** Icon ***********
		// Lampe AUS          0x80
		// Lampe EIN          0x81
		// OFFEN              0x82
		// geschlossen        0x83
		// fehler             0x84
		// alles ok           0x85
		// information        0x86
		// neue nachricht     0x87
		// servicemeldung     0x88
		// ohne Icon          ""
		//---------------------------------------
		// Umlaute - Sonderzeichen:
		//
		// "{" = "ä"
		// "|" = "ö"
		// "#" = "Ö"
		// "}" = "ü"
		// "$" = "Ü"
		// "_" = "ß"
		// ";" = Sanduhr
		// "<" = Pfeil nach unten
		// "=" = Pfeil nach oben
		// "@" = Pfeil nach rechts unten
		// ">" = Pfeil nach rechts oben
		//---------------------------------------
		//     Zugriff auf vordefinierte Texte
		//
		//     0x80 Textblock  1
		//     0x81 Textblock  2
		//     0x82 Textblock  3
		//     0x83 Textblock  4
		//     0x84 Textblock  5
		//     0x85 Textblock  6
		//     0x86 Textblock  7
		//     0x87 Textblock  8
		//     0x88 Textblock  9
		//     0x89 Textblock 10

		// Wandle Umlaute um (das Ä geht leider noch nicht. Scheint ein Bug zu sein.)
		$search  = array('ä', 'Ä','ö', 'Ö', 'ü', 'Ü', 'ß');
		$replace = array('{', 'A','|', '#', '}', '$', '_');
		$zeile1 = str_replace($search, $replace, $zeile1);
		$zeile2 = str_replace($search, $replace, $zeile2);
		$zeile3 = str_replace($search, $replace, $zeile3);

		// 1. Zeile *****************************
		$display_line[1] =
			array(    'text'    =>     $zeile1,
			'icon'    =>     $icon1);
		// 2. Zeile *****************************
		$display_line[2] =
			array(    'text'    =>     $zeile2,
			'icon'    =>     $icon2);
			// 3. Zeile *****************************
			$display_line[3] =
			array(    'text'    =>     $zeile3,
			'icon'    =>     $icon3);


		//*************************************************************************
		// Ab hier keine Änderungen machen
		//*************************************************************************

		$string = HMDIS_umsetzen($this->InstanceID, $display_line);

		// Definition der Werte für die Tonfolgen
		$string = $string . ",0x14," . $tonfolge . ",0x1C,";

		// Wiedeholungen und Abstand hinzufügen
		$string = HMDIS_wiederholungen_abstand($this->InstanceID, $wiederholungen, $string, $abstand);

		// Definition der Werte für die Signale
		$string = $string . $signal . ",0x03";

		$HM_Script = '
		! Hex-String an das Display senden
		dom.GetObject("BidCos-RF.'.$Seriennummer.':3.SUBMIT").State("'.$string.'");
		';

		//echo $HM_Script;

		HMDIS_HMRS_HTTP_Post($this->InstanceID, $CCU_IP, $HM_Script);

		}
	}
?>

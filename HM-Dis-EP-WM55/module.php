<?

	class HMDISEPWM55 extends IPSModule {

	public function Create() {

		parent::Create();

		$this->RegisterPropertyString("ipadress", "192.168.1.8");
		$this->RegisterPropertyString("serialnumber", "NEQ1593741");

	}

	public function ApplyChanges() {

		parent::ApplyChanges();

		$this->RegisterVariableInteger("actualpage", "Aktuelle Seite", "",1);

		if (($this->ReadPropertyString("ipadress") != "") and ($this->ReadPropertyString("serialnumber") != ""))
			{
				$this->SetStatus(102);
			} else {
				$this->SetStatus(202);
			}

	}

	protected function umsetzen($display_line) {

		$string = "0x02,0x0A";
		if ($display_line[1]['text'] != "") {
            $string .= ",0x12,".hex_encode($display_line[1]['text']);
            if ($display_line[1]['icon'] != ""){
                $string .= "0x13,".$display_line[1]['icon'].",0x0A";
            } else {
               $string .= "0x0A";
            }
        } else {
            $string.= ",0x0A";
        }
        if ($display_line[2]['text'] != "") {
            $string .= ",0x12,".hex_encode($display_line[2]['text']);
            if ($display_line[2]['icon'] != ""){
                $string .= "0x13,".$display_line[2]['icon'].",0x0A";
            } else {
               $string .= "0x0A";
            }
        } else {
            $string.= ",0x0A";
        }
        if ($display_line[3]['text'] != "") {
            $string .= ",0x12,".hex_encode($display_line[3]['text']);
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


protected function hex_encode($ascii) {
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


protected function HMRS_HTTP_Post($CCU_IP, $HM_Script) {
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

protected function wiederholungen_abstand($wiederholungen, $string, $abstand) {
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
	public function writeDisplay($zeile1, $zeile2, $zeile3) {

		// Lese & schreibe aktuelle Verbrauchsdaten
		$ip = $this->ReadPropertyString("ipadress");

		$CCU_IP = $this->ReadPropertyString("ipadress");  // IP der CCU2
$Seriennummer = $this->ReadPropertyString("serialnumber"); // Seriennummer des Display

//---------------------------------------
// Definition der Werte für die Signale
//
//  ! 0xF0 AUS
//  ! 0xF1 Rotes Blitzen
//  ! 0xF2 Grünes Blitzen
//  ! 0xF3 Orangenes Blitzen

$signal = "0xF0";

//---------------------------------------
// Definition der Werte für die Tonfolgen
//
//  ! 0xC0 AUS
//  ! 0xC1 LANG LANG
//  ! 0xC2 LANG KURZ
//  ! 0xC3 LANG KURZ KURZ
//  ! 0xC4 KURZ
//  ! 0xC5 KURZ KURZ
//  ! 0xC6 LANG

$tonfolge = "0xC0";

// Wiederholungen
// 1 bis 15. 0 = Unendlich
$wiederholungen = 1;

// es wird zum nächstmöglichen Abstand aufgerundet. Maximum ist 160s.
$abstand = 10;

//---------------------------------------
//    ******** Icon ***********
// EIN                icon_on
// AUS                icon_off
// OFFEN              icon_open
// geschlossen        icon_closed
// fehler             icon_error
// alles ok           icon_ok
// information        icon_information
// neue nachricht     icon_message
// servicemeldung     icon_service
// ohne Icon          icon_no
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

// 1. Zeile *****************************
$display_line[1] =
    array(    'text'    =>     $zeile1,
              'icon'    =>     "");
// 2. Zeile *****************************
$display_line[2] =
    array(    'text'    =>     $zeile2,
              'icon'    =>     "");
// 3. Zeile *****************************
$display_line[3] =
    array(    'text'    =>     $zeile3,
              'icon'    =>     "");


//*************************************************************************
// Ab hier keine Änderungen machen
//*************************************************************************

$string = umsetzen($display_line);

// Definition der Werte für die Tonfolgen
$string = $string . ",0x14," . $tonfolge . ",0x1C,";

// Wiedeholungen und Abstand hinzufügen
$string = wiederholungen_abstand($wiederholungen, $string, $abstand);

// Definition der Werte für die Signale
$string = $string . $signal . ",0x03";

$HM_Script = '
! Hex-String an das Display senden
dom.GetObject("BidCos-RF.'.$Seriennummer.':3.SUBMIT").State("'.$string.'");
';

//echo $HM_Script;

HMRS_HTTP_Post($CCU_IP, $HM_Script);

	}
?>

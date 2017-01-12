# Symcon-HM-Dis-EP-WM55
Dieses Modul soll nur übergangsweise bestehen bis die Unterstüzung in das Homematic Extended Modul oder in IPSymcon selber implementiert wurde.

Der absolute Großteil der Funktionen stammt nicht von mir, sondern von User Chris aka. "balihannes" aus dem IPSymcon Forum. Vielen Dank dafür!

##Ausgabe von Text, Icons und Sound auf einem Homematic HM-Dis-EP-WM55
Befehl: `HMDIS_writeDisplay(zeile1, zeile2, zeile3, icon1, icon2, icon3, signal, tonfolge );`

###zeile 1-3
Gibt entsprechend den Inhalt der Variable auf der jeweiligen Zeile aus.

###icon 1-3
Gibt ein Icon am Ende der jeweilgen Zeile 1-3 aus.

###signal
Lässt die LED aufleuchten.
```
0xF0 = Aus
0xF1 = kurzes rotes Aufleuchten
0xF2 = kurzes grünes Aufleuchten
0xF3 = kurzes orangenes Aufleuchten
```

###tonfolge
Gibt einen oder mehrere Töne aus.
```
0xC0 = Aus
0xC1 = Lang lang
0xC2 = Lang kurz
0xC3 = Lang kurz kurz
0xC4 = Kurz
0xC5 = Kurz kurz
0xC6 = Lang
```

###Beispiel
`HMDIS_writeDisplay(45220 /*[Devices\Homematic\Taster \Homematic HM-DIS-EP-WM55]*/,"Es geht!", "Test Zeile 2", "Test Zeile 3" , "0x85", "0x86", "0x87", "0XF3", "0xC4");`

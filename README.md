# Computer Networks: TCP-socket-server-and-client-implementation-in-PHP

## Përshkrimi i Projektit
Ky projekt implementon një server dhe klient që komunikojnë përmes socket TCP duke përdorur PHP. Ai demonstron një sistem bazë për komunikim në rrjet dhe lehtëson kuptimin e funksionalitetit të sockets në PHP. 

Projekti është i dobishëm për ata që duan të mësojnë se si të ndërtojnë sisteme që përdorin protokollin TCP/IP për shkëmbimin e të dhënave.

## Struktura e Projektit
Projekti përmban komponentët e mëposhtëm:

- **Server.php**: Ky skedar inicializon serverin që pret lidhje nga klientët dhe përpunon të dhënat që marrin.
- **Client.php**: Ky skedar përfaqëson një klient që lidhet me serverin dhe shkëmben të dhëna me të.
- **ClientsWithPermissions.php**: Përmban një mekanizëm për menaxhimin e klientëve dhe lejeve të tyre.
- **Files/**: Një dosje me skedarë testimi si `file1.txt` dhe `file2.txt` për të demonstruar shkëmbimin e të dhënave midis klientit dhe serverit.

## Si të Përdoret

1. **Nisni serverin**:
   Hapni një terminal dhe shkruani komandën:
   ```bash
   php Server.php
   ```
   Serveri do të fillojë të dëgjojë lidhjet e klientëve.

2. **Nisni klientin**:
   Hapni një terminal tjetër dhe shkruani komandën:
   ```bash
   php Client.php
   ```
   Klienti do të lidhet me serverin dhe do të mund të shkëmbejë të dhëna.

3. **Testoni me skedarë**:
   Mund të përdorni skedarët brenda dosjes `Files/` për të parë sesi serveri dhe klienti i trajtojnë të dhënat.

## Shembull i Përdorimit
### Server
Në terminalin e serverit:
```
Serveri është duke dëgjuar për lidhje...
Klienti u lidh nga: 127.0.0.1
Mesazhi i pranuar: Përshëndetje nga klienti!
Mesazhi i kthyer: Përshëndetje nga serveri!
```

### Klienti
Në terminalin e klientit:
```
Lidhja me serverin...
Mesazhi i dërguar: Përshëndetje nga klienti!
Përgjigja e serverit: Përshëndetje nga serveri!
```

## Karakteristikat Kryesore
- Komunikim i besueshëm me socket TCP.
- Menaxhimi i lidhjeve të shumëfishta të klientëve.
- Leximi dhe shkëmbimi i të dhënave nga/tek skedarët.


## Teknologjitë e Përdorura
- **PHP**: Për ndërtimin e serverit dhe klientit.
- **Sockets TCP**: Për komunikim në rrjet.

## Profesorë dhe Asistentë
- **Profesor**: Prof. Dr. Blerim Rexha
- **Asistent**: Dr. Sc. Mërgim H. HOTI

## Kontribuesit
Ky projekt është zhvilluar nga studentët e mëposhtëm:

- [Brilant Ponxhaj](https://github.com/BrilantPonxhaj)
- [Brikenda Zogaj](https://github.com/Brikenda-Zogaj)
- [Brinte Uka](https://github.com/BrinetaUka)
- [Clirimtar Citaku](https://github.com/clirimtar-citaku)

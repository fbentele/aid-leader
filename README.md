# aid-leader
WordPress Leiter Plugin für Midata-Sync

## Todo: add meaningful readme...

## Development/Testing
### Plugin in Wordpress Docker-Container
Mithilfe von [Docker-Compose](https://docs.docker.com/compose/) kann eine Wordpress-Installation gestartet werden, welche gleich die aktuellste Version des Plugins enthält.
>Wichtig: Diese Konfiguration sollte nur für die Entwicklung verwendet werden, da sie nicht abgesichert ist.

Führe dazu im Verzeichnis mit dem `docker-compose.yml` folgenden Befehl aus:

```
docker-compose up
```

Danach kann über http://localhost:8080 das Setup ausgeführt werden. Mit `ctrl+C` werden die Container wieder beendet. Falls die Container bereits existieren, können diese wiederverwendet werden und das Setup ist überflüssig.

**Todo: Ergänzen, wie API-Key erzeugt und hinterlegt werden kann.**

Änderungen am Plugin sind erst nach einem erneuten Build des Images sichtbar. Nutze dazu folgenden Befehl:
```
docker-compose build && docker-compose up
```

Nach dem Testing können die erzeugten Container entfernt werden:

```
docker-compose down
```


### Hitobito
Um das Development zu vereinfachen und ohne die produktiven Systeme zu beeinflussen, kann Hitobito auch als Docker-Container ausgeführt werden.
Dazu kann das Image aus dem Repository https://github.com/rehrbar/hitobito-dev erzeugt werden, welches gleich die Struktur und Testdaten für Midata enthält.
>Wichtig: Diese Konfiguration sollte nur für die Entwicklung verwendet werden, da sie nicht abgesichert ist.
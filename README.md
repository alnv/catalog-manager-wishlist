# Catalog Manager - Die Wunschliste Erweiterung

1) Als erstes muss die Wunschliste im gewünschten Frontend Modul aktiviert werden. 
Mehr auf: https://catalog-manager.org/dokumentation/catalog-manager-wunschliste.html

2) Im entsprechenden Template muss das Wunschlisten Formular ausgegeben werden. Das Formular befindet sich in der "wishlistForm" Varaiblen. 
Diese Variable lässt sich bei folgenden Templates aufrufen:
- ctlg_view_teaser
- ctlg_view_master
- ctlg_view_table

Ein Beispiel mit "ctlg_view_teaser":

``` php
<?php if ( $this->useWishlist ): ?>
    <?= $this->wishlistForm ?>
<?php endif; ?>
```

## Verfügbare Inserttags:

### {{WISHLIST}} 
Gibt die gesamte Wunschliste tabellarisch zurück. Diese kann an eine E-Mail angehängt werden.
Weitere Optionen:

- **{{WISHLIST::?tables=ctlg_my_table}}** => Damit können wir nur bestimmte Tabellen ausgeben.


### {{WISHLIST_AMOUNT::ctlg_my_table}} 
Gibt die Anzahl der sich befindeten Datensätze in der Wunschliste zurück.

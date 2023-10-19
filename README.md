# Projet Recherche des mots clées

Ce projet permet de povuoir ajoiter en récurisvité des documents où une tokenisation va être effectué pour garder que les mots clées.
Par la suite on peut chercher les mot clée et voir dans quels documents ils appareissents et le nombre d'apparation dans le document.

## Structure du projet

Le dossier CSS: contien le style
<br>
Le dossiser readFiles juste un dossier qui peut être utilisée d'exemple pour la récursivité pour la tokenisation<br>
Le dossier uploads : contient les documents qui ont été insérée<br>
Le fichier "bdd.php" contient toute les fonctions qui font appelle à la base de donnée (l'ajout des mots, la recherche des mots ect..)<br>
Le fichier "form.html" où se trouve le formulaire d'insertion
<br>
Le fichier "stopword.txt" contient tous les mots unitiles.
<br>
Le fichier "showDocumentFind.php" permet d'affichier le tableau avec les documents trouvé à partir du mot clé.
<br>
Le fichier "showContentFile.php" permet de voire le contenue du fichier que nous avons sélectionnés après le résultat de la recherche du mot clé.
<br>
Le fichier utils.php contient les diffentes fonctions comme la tokenisation() ou la recusivité

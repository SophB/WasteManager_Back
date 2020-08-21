Waste Manager

Contexte
Afin d'aider les communes à améliorer leur gestion des déchets, vous devez programmer
un logiciel qui aura pour fonction d'assigner les différents déchets générés par les
acteur.ices de la commune aux service de traitement des déchets disponibles.
Plusieurs types de déchets existes pour le moment, mais il est probable que d'autres
se rajoutent par la suite (la conception devra le prendre en compte) :

Déchets gris, le déchet de base
Déchets plastiques, qui seront de différents types
Déchets cartons
Déchets organiques

Plusieurs services de traitements des déchets seront disponibles (pareillement, d'autres peuvent exister par la suite) :

Incinérateur, qui accepte tout type de déchets
Centre de tri des déchets recyclables, se charge de trier les déchets pour les envoyer aux centres de traitements adequat (optionnel mais intéressant, rajoute un intermédiaire)
Centre de recyclage, peuvent traiter carton et/ou plastiques, potentiellement selon leur type
Composteurs de quartier, peuvent traiter les déchets compostables

Chacun de ces services de traitement a une capacité de traitement définie en tonne
et rejette du CO2 relatif au nombre de déchets traités.

Mise en oeuvre
En utilisant le langage objet que vous souhaitez (défaut PHP), vous allez devoir créer
un programme qui acceptera en entrée un fichier json (fourni dans ce dépôt), contenant les
déchets ainsi que les services de traitement des déchets disponibles, et afficher le résultat
de la répartition des déchets ainsi que le CO2 rejetté.
L'idéal est de favoriser les traitement adaptés et de ne se replier sur les autres méthodes de
traitement que lorsque les capacités maximales d'un services sont atteinte.
Vous allez devoir utiliser les différents principes de la POO (SOLID) et ses outils
(classes, héritage, interfaces, abstraction, etc.) pour réaliser ce programme.
Commencer par créer un diagramme de classe.
Une étape supplémentaire pourra être de gérer l'output du programme (JSON, XML, HTML, etc.),
lui aussi via des principes de POO

C02
Ci dessous des valeurs bidon de CO2 rejettés pour les différents déchets via leur mode de traitement, expimé en kg de CO2/tonne de déchet traité

Déchets gris : Incinération (30)
Déchets plastiques

PET : Incinération (40) Recyclage (8)
PVC : Incinération (38) Recyclage (12)
PC : Incinération (42) Recyclage (10)
PEHD : Incinération (35) Recyclage (11)


Déchets Carton : Incinération (25) Recyclage (5)
Déchets organique : Incinération (28) Compostage (1)
Métaux : Incinération (50) Recylcage (7)
Verre : Incinération (50) Recyclage (6)


Intégration dans Symfony
En essayant de respecter une architecture SOLID, faire en sorte d'intégrer ces classes
de traitement du JSON dans une application Symfony qui permettra :

De fournir les data via un fichier JSON dans un formulaire et d'afficher le résultat sous forme de graphique
De fournir les data via du JSON via une API Rest, qui nous renverra le résultat sous forme de JSON
Dans les deux cas, les resultats seront sauvegardés en base de données dans un rapport daté (il pourra avoir une propriété contenant directement le json)
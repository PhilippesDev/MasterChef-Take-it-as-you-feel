# Masterchef : Take it as you feel !

Commandez votre plat comme vous l'imaginez, pas comme on vous le sert.

Masterchef est un site de commande de repas où c'est le client qui écrit la recette. Sel, sucre, piment, vinaigre, citron, cuisson : tout se règle avant de commander, puis une cuisine prépare le plat exactement comme demandé et le fait livrer.

Projet réalisé pour le cours d'e-commerce à l'ISIG-Goma.

## D'où vient l'idée

Elle part d'une petite frustration, celle de tous ceux qui commandent à manger. On choisit un plat sur un menu et on le reçoit tel quel. Trop salé, pas assez relevé, une sauce qu'on n'aime pas, et personne pour en tenir compte.

Un exemple concret nous a servi de départ : un burger avec du citron raffiné sur les épices. À Goma, aucun restaurant ne le propose, et aucun ne vous laisse le demander. On s'est dit que le problème n'était pas la cuisine, mais le fait que le client n'a pas son mot à dire.

## Le problème

- Les menus sont figés. Si un plat ne convient pas à 100 %, on le mange quand même ou on change de plat.
- Demander une modification par téléphone ou par message est aléatoire. Elle se perd, elle est mal comprise, ou on vous répond que ce n'est pas possible.
- Les goûts varient énormément d'une personne à l'autre (le sel, le sucre, le piquant, l'acidité), mais la plupart des offres de livraison les ignorent.

## Notre solution

Un site où le plat se construit étape par étape. Le client choisit une base (un burger, par exemple), règle chaque paramètre avec des curseurs et des options, ajoute une remarque libre s'il le souhaite, et valide. Sa commande part dans une file d'attente. Un chef la prend en charge, cuisine selon la fiche exacte du client, et marque le plat comme prêt. Le client reçoit une notification, puis la livraison commence.

Le point important : les réglages ne sont pas un simple commentaire perdu au bas d'un bon de commande. Ils font partie de la commande, et le chef les voit clairement sur sa fiche.

## Comment ça marche

1. Le client crée un compte et se connecte.
2. Il choisit un plat de base dans le catalogue.
3. Il le personnalise : quantité de sel, de sucre, niveau de piquant, citron, vinaigre, épices, cuisson, ingrédients à ajouter ou à retirer. Une zone de texte libre permet de préciser le reste.
4. Le prix se met à jour pendant qu'il règle son plat, parce que certaines options coûtent un supplément.
5. Il passe au paiement (simulé pour le moment) et valide.
6. La commande entre dans la file d'attente de la cuisine.
7. Un chef la prend, prépare le plat et indique quand il est prêt.
8. Le client est prévenu, puis un livreur récupère la commande et l'apporte.
9. Après réception, le client peut laisser un avis sur son plat.

## Qui fait quoi

**Le client** compose son plat, paie, suit sa commande et donne son avis.

**Le chef** voit la liste des commandes en attente, ouvre la fiche détaillée d'une commande, la prend en charge, cuisine et signale quand c'est prêt.

**Le livreur** voit les commandes prêtes, en prend une, la livre et confirme la remise.

**L'administrateur** gère le catalogue des plats et des options, les prix, les comptes des chefs et des livreurs, et suit l'activité (nombre de commandes, plats les plus demandés, délais moyens).

## Les réglages possibles

Chaque plat de base propose ses propres options, selon ce qui a du sens pour lui. En général on retrouve :

- l'assaisonnement : sel, sucre, piment, épices ;
- l'acidité : citron, vinaigre ;
- la cuisson : par exemple saignant, à point ou bien cuit pour une viande ;
- les ingrédients : ceux qu'on veut ajouter, ceux qu'on veut retirer ;
- une note libre, pour tout ce qui n'entre pas dans une case.

## Le parcours d'une commande

Une commande passe par plusieurs statuts, toujours dans le même ordre :

`en attente` → `en préparation` → `prête` → `en livraison` → `livrée`

Elle peut aussi être `annulée`, tant qu'un chef ne l'a pas encore prise en charge. Chaque changement de statut déclenche une notification au client.

## Partie technique

Cette partie décrit le fonctionnement interne du projet. Les choix d'outils et de configuration seront ajoutés plus tard.

### Organisation générale

Le projet se découpe en quatre espaces : l'espace client, l'espace cuisine, l'espace livraison et l'espace d'administration. Tous s'appuient sur les mêmes règles métier (calcul du prix, changement de statut, droits d'accès) et sur la même base de données. Chaque utilisateur n'a accès qu'à l'espace qui correspond à son rôle.

### Données

On y retrouve, en gros :

- les utilisateurs, avec leur rôle (client, chef, livreur, administrateur) ;
- les plats de base, les ingrédients et les options de personnalisation proposées pour chaque plat ;
- les commandes et leurs lignes ;
- les personnalisations d'une commande, enregistrées une par une (par exemple : sel = faible, citron = oui, cuisson = à point). C'est cette table qui rend chaque plat unique et qui alimente la fiche du chef ;
- les paiements, les livraisons, les avis et les notifications.

### La file d'attente

Les commandes sont traitées dans l'ordre d'arrivée. Quand un chef prend une commande, elle lui est attribuée tout de suite, pour que deux chefs ne cuisinent pas le même plat. Le temps d'attente affiché au client est une estimation : le nombre de commandes devant la sienne, multiplié par un temps moyen de préparation.

### Calcul du prix

Le prix se compose du prix de base, plus les suppléments des options choisies. Il est affiché en direct pendant la personnalisation, mais il est recalculé au moment de la validation, à partir des données enregistrées. On ne se fie pas au total affiché à l'écran.

### Notifications

Un message est envoyé au client à chaque étape importante : commande reçue, prise en charge, plat prêt, livraison en cours, livraison terminée.

### Droits d'accès

Un client ne voit que ses propres commandes. Un chef ne voit que les commandes à préparer et celles qu'il a prises. Un livreur ne voit que ce qui concerne la livraison. Seul l'administrateur voit l'ensemble.

### Paiement

Le paiement est simulé pour la démonstration, sur le modèle du Mobile Money. Aucun vrai argent ne circule dans cette version.

## Limites actuelles

- Les plats sont préparés par des chefs humains. L'idée de cuisine automatisée reste une piste pour plus tard.
- La livraison est pensée pour une zone limitée de la ville.
- Les options sont définies à l'avance par l'administrateur, le client ne peut pas encore inventer un ingrédient de toutes pièces.

## Pistes pour la suite

- Sauvegarder « ses recettes » pour recommander son plat en un clic.
- Partager une recette avec d'autres clients.
- Des abonnements pour recevoir son plat préféré chaque semaine.
- Une cuisine partiellement automatisée.

## Installation

À venir.

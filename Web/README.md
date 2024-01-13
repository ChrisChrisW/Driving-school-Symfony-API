# Technologies

    * Symfony 6.2
    * PHP 8.1
    * Composer
    * PostgresSQL
    * HTML / CSS / Javascript
    * Make (Makefile) de préférence sinon il faut se référer aux détails d'installations tout en bas

Le projet est scindé en deux parties un back-end avec Symfony et API Platform et un front-end en HTML/CSS/Javacript.


# Démarrer le projet

1. Créer un utilisateur sur Postgres

2. Créer un base de donnée **bd_autoecole** avec le nom de l'utilisateur

3. Créer un **.env.local** dans **./Api/** et ajouter : 
> DATABASE_URL="postgresql://username:password@127.0.0.1:5432/bd_autoecole?serverVersion=14&charset=utf8"

_NB : Ne pas oublier de modifier le username et le password._

4. Contruire la base de donnée
```sh
# Permet de créer les tables de la base de donnée et d'insérer quelques données dans BD
make build
```

5. Allumer les server local de l'API
```sh
# Permet de démarrer les server localhost de l'API
make api-start
# Permet de démarrer le site web
make ou make all
```

NB : en cas de soucis il faut faire `make update`


---

# Détails d'installation pour l'API en général

1. Aller dans le dossier API
2. Téléchargez et installez Symfony en suivant les instructions sur leur site web : [Symfony]("https://symfony.com/")
2. Configurez la connexion à la base de données Postgres en modifiant les paramètres de l'URL de la connexion dans le fichier .env
3. Utilisez la commande `php bin/console make:migration` pour générer les migrations pour la base de données.
4. Exécutez les migrations avec la commande `php bin/console doctrine:migrations:migrate` pour créer les tables dans la base de données.
5. Exécutez les migrations avec la commande `php bin/console doctrine:fixtures:load` pour charger les fixtures.
6. Démarrez le serveur en utilisant la commande `symfony server:start` ou `php bin/console server:start`
7. Dirigez vous sur ["http://localhost:8000/api"]("http://localhost:8000/api")



# Détails d'installation pour la page web en général

1. Démarrez le serveur en utilisant la commande `php -S localhost:3000`
2. Dirigez vous sur ["http://localhost:3000"]("http://localhost:3000")



# Équipe

    - Kanaïe Atrian Sami
    - MOHAMMAD Usman
    - WANG Christophe


---
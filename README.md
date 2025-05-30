Pour configurer un projet Laravel après avoir cloné le dépôt depuis Git, suivez ces étapes :

1. **Assurez-vous que vous avez les prérequis :**
   - PHP (version recommandée par Laravel, généralement 8.x ou 9.x).
   - Composer (gestionnaire de dépendances PHP).
   - Un serveur de base de données (comme MySQL, PostgreSQL, SQLite, etc.).
   - (Optionnel mais recommandé) Un serveur web comme Nginx ou Apache pour le déploiement.

2. **Clonez le dépôt si ce n'est pas déjà fait :**
   ```bash
   git clone https://github.com/Pamoja-Solution/Boutique-Suite.git
   cd Boutique-Suite
   ```

3. **Installez les dépendances du projet :**
   Exécutez la commande suivante pour installer les dépendances PHP définies dans le fichier `composer.json` :
   ```bash
   composer install
   ```

4. **Configurez le fichier `.env` :**
   - Copiez le fichier `.env.example` en `.env` si ce fichier n'existe pas encore :
     ```bash
     cp .env.example .env
     ```
   - Ouvrez le fichier `.env` et configurez les paramètres de votre base de données et autres configurations spécifiques au projet (comme les clés API, les paramètres de messagerie, etc.). Les informations typiques incluent :
     ```dotenv
     APP_TIMEZONE=Africa/Kinshasa
        APP_CURRENCY=CDF

        APP_LOCALE=fr

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nom_de_la_base
     DB_USERNAME=utilisateur
     DB_PASSWORD=mot_de_passe
     ```

5. **Générez une clé d'application :**
   Laravel utilise une clé secrète pour sécuriser les sessions et autres fonctionnalités. Générez cette clé avec la commande suivante :
   ```bash
   php artisan key:generate
   ```

6. **Exécutez les migrations de base de données (si nécessaire) :**
   Si le projet utilise des migrations pour définir la structure de la base de données, exécutez-les avec :
   ```bash
   php artisan migrate
   ```

7. **Créez les fichiers de cache et les configurations :**
   Vous pouvez exécuter les commandes suivantes pour vous assurer que toutes les configurations et caches sont bien en place :
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Lancez le serveur de développement (optionnel) :**
   Vous pouvez tester le projet localement en utilisant le serveur de développement intégré de Laravel :
   ```bash
   php artisan serve
   ```
   Ensuite, ouvrez votre navigateur et accédez à `http://localhost:8000` pour voir si l'application fonctionne correctement.

9. **Configurez les permissions des répertoires (si nécessaire) :**
   Assurez-vous que les répertoires `storage` et `bootstrap/cache` sont accessibles en écriture :
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

10. **Dépendances front-end (si nécessaire) :**
    Si le projet utilise des outils comme Laravel Mix pour le build des assets front-end, installez les dépendances Node.js et compilez les assets :
    ```bash
    npm install
    npm run dev
    ```

Après avoir suivi ces étapes, votre projet Laravel devrait être correctement configuré et prêt à être utilisé ou développé. Si vous rencontrez des erreurs spécifiques, vérifiez les messages d'erreur et consultez la documentation officielle ou les fichiers README du projet pour des instructions supplémentaires.



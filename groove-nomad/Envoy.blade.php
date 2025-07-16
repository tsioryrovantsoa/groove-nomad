@include('vendor/autoload.php')

@setup
    Dotenv\Dotenv::create(Illuminate\Support\Env::getRepository(), __DIR__)->load();
    $project = env('APP_NAME');
    $project_link = env('DEPLOY_URL');
    $repository = env('DEPLOY_REPOSITORY');
    $base_dir = env('DEPLOY_BASE_DIR');
    $releases_dir = $base_dir . '/releases';
    $app_dir = $base_dir . '/current';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir . '/' . $release . '/' . 'groove-nomad';
    $discord_webhook = env('DISCORD_WEBHOOK');
    $deployer = exec('whoami');
    $deployer_clean = str_replace('\\', '\\\\', $deployer);
    $branch = trim(exec('git rev-parse --abbrev-ref HEAD'));
    $commit = trim(exec('git log --pretty=format:"%h" -n1 HEAD'));
    $commit_link = env('DEPLOY_REPOSITORY_URL') . $commit;
    $key = env('DEPLOY_ENV_KEY');
    $keep_releases = 0;
@endsetup

@servers(['production' => env('DEPLOY_USER') . '@' . env('DEPLOY_HOST'), 'localhost' => '127.0.0.1'])

@story('deploy')
    confirm
    clone_repository
    run_composer
    update_symlinks
    run_migrations
    optimize
    cleanup
@endstory

@story('deploy:first')
    confirm
    init_directories
    clone_repository
    run_composer
    init_env
    update_symlinks
    init_storage
    run_migrations
    optimize
@endstory

@story('deploy:rollback')
    confirm
    rollback
@endstory

@story('deploy:optimize')
    confirm
    optimize_only
@endstory

@task('confirm', ['on' => 'production', 'confirm' => true])
    echo "Confirmation..."
@endtask

@task('init_directories', ['on' => 'production'])
    echo "Création des répertoires initiaux..."
    mkdir -p {{ $releases_dir }}
    mkdir -p {{ $base_dir }}/storage/app
    mkdir -p {{ $base_dir }}/storage/framework/cache
    mkdir -p {{ $base_dir }}/storage/framework/sessions
    mkdir -p {{ $base_dir }}/storage/framework/views
    mkdir -p {{ $base_dir }}/storage/logs
    chmod -R 775 {{ $base_dir }}/storage
@endtask

@task('init_env', ['on' => 'production'])
    echo "Configuration initiale de l'environnement..."
    if [ ! -f {{ $base_dir }}/.env ]; then
    cd {{ $new_release_dir }}

    # Décrypter l'environnement
    php artisan env:decrypt --env=production --key={{ $key }} --force

    # Assurez-vous que le fichier .env est bien présent dans le répertoire de la nouvelle release
    cp .env.production .env

    # Générer la clé avec le fichier .env local
    php artisan key:generate --force

    # Copier le fichier .env configuré vers le répertoire de base
    cp .env {{ $base_dir }}/.env

    echo "Fichier .env créé et clé générée !"

    else
    echo "Le fichier .env existe déjà."
    fi
@endtask

@task('init_storage', ['on' => 'production'])
    echo "Configuration initiale du stockage..."
    if [ ! -d {{ $base_dir }}/storage ]; then
    cp -r {{ $new_release_dir }}/storage {{ $base_dir }}/storage
    echo "Dossier storage créé !"
    else
    echo "Le dossier storage existe déjà."
    fi
    chmod -R 775 {{ $base_dir }}/storage
@endtask

@task('clone_repository', ['on' => 'production'])
    echo 'Clonage du repository...'
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git reset --hard origin/main
    echo "Repository cloné avec succès !"
@endtask

@task('run_composer', ['on' => 'production'])
    echo "Installation des dépendances..."
    cd {{ $new_release_dir }}
    composer install --no-dev --optimize-autoloader
    echo "Dépendances installées avec succès !"
@endtask

@task('update_symlinks', ['on' => 'production'])
    echo "Mise à jour des liens symboliques..."
    # Création des liens symboliques pour .env et storage
    ln -nfs {{ $base_dir }}/.env {{ $new_release_dir }}/.env
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $base_dir }}/storage {{ $new_release_dir }}/storage

    # Définition des permissions
    chmod -R 775 {{ $new_release_dir }}/bootstrap/cache

    # Création du lien symbolique pour l'application actuelle
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}
    echo "Liens symboliques mis à jour avec succès !"
@endtask

@task('run_migrations', ['on' => 'production'])
    echo "Exécution des migrations..."
    cd {{ $new_release_dir }}
    php artisan migrate --force
    echo "Migrations exécutées avec succès !"
@endtask

@task('optimize', ['on' => 'production'])
    echo "Optimisation de l'application..."
    cd {{ $new_release_dir }}
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    echo "Application optimisée avec succès !"
@endtask

@task('optimize_only', ['on' => 'production'])
    echo "Optimisation de l'application actuelle..."
    cd {{ $app_dir }}
    php artisan config:cache --force
    php artisan route:cache --force
    php artisan view:cache --force
    php artisan optimize --force
    echo "Application optimisée avec succès !"
@endtask

@task('cleanup', ['on' => 'production'])
    echo "Nettoyage des anciennes versions..."

    cd {{ $releases_dir }}
    ls -dt */ | tail -n +{{ $keep_releases + 2 }} | xargs rm -rf

    echo "Nettoyage terminé !"
@endtask

@task('rollback', ['on' => 'production'])
    echo "Restauration à la version précédente..."
    cd {{ $releases_dir }}
    # Trouver l'avant-dernière version
    last_release=$(ls -dt */ | head -n 2 | tail -n 1)

    if [ -d "{{ $releases_dir }}/$last_release" ]; then
    ln -nfs {{ $releases_dir }}/$last_release {{ $app_dir }}
    echo "Rollback effectué vers la version $last_release"

    # Optimiser la version restaurée
    cd {{ $app_dir }}
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    echo "Version restaurée optimisée avec succès !"
    else
    echo "Aucune version précédente trouvée pour le rollback."
    fi
@endtask

@error
    @discord($discord_webhook, "❌ Échec du déploiement !\n- 📂 Projet : [$project]($project_link)\n- 🔄 Tâche : $__task\n- 🌿 Branche : $branch\n- 💾 Commit : [$commit]($commit_link)\n- 🏷 Release : $release\n- 👤 Par : $deployer_clean")
@enderror

@finished
    if ($exitCode === 0 ||$exitCode === null) {
    @discord($discord_webhook, "🚀 Déploiement effectué avec succès !\n- 📂 Projet : [$project]($project_link)\n- 🔄 Tâche : $__task\n- 🌿 Branche : $branch\n- 💾 Commit : [$commit]($commit_link)\n- 🏷 Release : $release\n- 👤 Par : $deployer_clean")
    } else {
    echo "Deployment failed with status code: $status";
    }
@endfinished

<?php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'Solene');

// Config

set('repository', 'https://github.com/tsioryrovantsoa/solene.git');

set('thedev', fn() => runLocally('/usr/bin/whoami'));

set('discord_webhook_url', 'https://discord.com/api/webhooks/1302993838314491944/W4-XU9Hpp_2KmJFkRFHF7Mmbtq6H2oCQyILdtNpiD2sY_fiAoy6q5LEFM8efiBQ4z94n');

add('shared_files', ['.env']);
add('shared_dirs', ['storage']);
add('writable_dirs', []);

set('current_path', fn() => get('deploy_path') . '/current');
set('shared_path', fn() => get('deploy_path') . '/shared');

# getsockename failed : not a socket
set('ssh_multiplexing', false);

# Lien symbolique vers la nouvelle version
set('use_atomic_symlink', true);

# 1 seul version sur le serveur
// Nombre de releases à conserver
set('keep_releases', 0);

# désactive l'envoi de statistiques anonymes à l'équipe Deployer.
set('allow_anonymous_stats', false);

# installer les dépendances de manière efficace et sans intervention humaine, tout en optimisant les performances pour un environnement de production
set('composer_options', '--verbose --prefer-dist --no-interaction --ignore-platform-reqs');

#erreur : http_user config
set('http_user', 'make4786');

#erreur : acl

set('writable_mode', 'chmod');

set('update_code_strategy', 'clone');

task('test:whoami', function () {
    writeln(run('whoami'));
});

// Fonction pour envoyer les notifications à Discord
task('notify:discord', function () {
    $message = get('discord_message');
    $webhookUrl = get('discord_webhook_url');
    $release_name = get('release_name');
    $application = get('application');
    $host = get('alias');

    run("curl -H 'Content-Type: application/json' -d '{\"username\":\"$application v$release_name\", \"content\": \"$message (Hôte : $host)\"}' $webhookUrl");
});


// Définir les messages de notification
task('notify:discord_start', function () {
    set('discord_message', "🚀 Déploiement de " . get('application') . " sur la branche " . get('branch') . " démarré");
    invoke('notify:discord');
});

task('notify:discord_failed', function () {
    set('discord_message', "❌ Échec du déploiement de " . get('application') . " sur la branche " . get('branch'));
    invoke('notify:discord');
});

task('notify:discord_success', function () {
    set('discord_message', "✅ Déploiement de " . get('application') . " sur la branche " . get('branch') . " terminé avec succès");
    invoke('notify:discord');
});

// Hosts

host('solene.tsiorydev.com')
    ->setHostname('109.234.167.11')
    ->set('remote_user', 'make4786')
    ->set('deploy_path', '/home/make4786/solene.tsiorydev.com');

task('check:confirm_deploy', function () {
    if (!askConfirmation("Avez-vous déjà construit et commité les fichiers statiques ? Voulez-vous continuer le déploiement ?", true)) {
        writeln("❌ Déploiement annulé par l'utilisateur.");
        exit;
    }
})->desc('Vérification avant le déploiement');

// Hooks

after('deploy:failed', 'deploy:unlock');
before('deploy', 'notify:discord_start');
before('deploy', 'check:confirm_deploy');
// before('deploy:update_code', 'build:assets');
after('deploy:failed', 'notify:discord_failed');
after('deploy:success', 'notify:discord_success');
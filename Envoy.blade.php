@setup

if(!isset($target)) {
$target = 'dev';
echo("\n\n        WARNING: No target deployment environment specified - deploying to staging by default.\n\n\n");
}

require('vendor/autoload.php');
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


$repo = 'git@github.com:MoveoTech/Foody.git';

$theme_dir = 'web/app/themes/Foody';
$release_dir = '/home/ubuntu/releases';
$db_backup_dir = '/var/www/db_backups';
$app_dir = '/var/www/html';
$global_uploads_dir = '/home/ubuntu/uploads';
$deploy_date = date('YmdHis');
$release = 'release_' . $deploy_date;
$shared_drive = '/home/ubuntu/foody-shared-efs/';

if( $target === 'production' ){
    echo 'Deploy to production';
    $app_dir = $shared_drive . 'foody.co.il/html';
    $global_uploads_dir = $shared_drive . 'uploads';
    $release_dir = $shared_drive . 'releases';
}

$app_uploads_dir = $app_dir . '/web/app/uploads';

$servers = [
    'local' => '127.0.0.1',
    'dev' => 'ubuntu@foody-dev.moveodevelop.com',
    'mu-dev' => 'ubuntu@mu.foody-dev.moveodevelop.com',
    'medio-dev' => 'ubuntu@foody.moveodevelop.com',
    'production' => 'ubuntu@34.253.214.81'
];

if (!isset($branch)){
    $branch = 'staging';
}

@endsetup

@servers($servers)


@story('deploy')
upload_compiled_assets
fetch_repo
run_install
run_after_install
@endstory


@task('upload_compiled_assets', ['on' => 'local'])
cd {{ $theme_dir }}
npm run production
tar -czf assets-{{ $release }}.tar.gz dist
scp assets-{{  $release }}.tar.gz {{ $servers[$target] }}:~
scp ./build/version-hash.txt {{ $servers[$target] }}:~
rm -rf assets-{{  $release }}.tar.gz

wp plugin list --format=json > ./plugins-export.json
scp ./plugins-export.json {{ $servers[$target] }}:~
rm ./plugins-export.json
@endtask


@task('fetch_repo', [ 'on' => $target ])
[ -d {{ $release_dir }} ] || sudo mkdir {{ $release_dir }};
cd {{ $release_dir }};
git clone --single-branch -b  {{$branch}} {{ $repo }} {{ $release }};
@endtask


@task('run_install', [ 'on' => $target ])
cd {{ $release_dir }}/{{ $release }};
cp ~/.env .
composer install --prefer-dist;
@endtask


@task('run_after_install', [ 'on' => $target ])

echo 'Copying w3tc config files to temp...'
[[ -e {{ $app_dir }}/web/app/advanced-cache.php ]] && cp {{ $app_dir }}/web/app/advanced-cache.php /tmp
[[ -e {{ $app_dir }}/web/app/object-cache.php ]] && cp {{ $app_dir }}/web/app/object-cache.php /tmp

echo 'Installing compiled assets...'
cd ~
tar -xzf assets-{{ $release }}.tar.gz -C {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}
sudo rm -rf assets-{{ $release }}.tar.gz

mv version-hash.txt {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}/build/

cd {{ $release_dir }}/{{ $release }};

echo 'Setting permissions...'
cd {{ $release_dir }};

sudo chgrp -R www-data {{ $release }};
sudo chmod -R ug+rwx {{ $release }};

cd {{ $release_dir }}/{{ $release }};

wp foody-cli activate_plugins ~/plugins-export.json --require=web/app/mu-plugins/foody-cli/foody-cli.php

echo 'Updating symlinks...'
sudo ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }};
sudo rm -r {{ $app_uploads_dir }}
sudo ln -s {{$global_uploads_dir}} {{$app_uploads_dir}}

echo 'Copying w3tc config files back from temp...'
[[ -e /tmp/advanced-cache.php ]] && cp /tmp/advanced-cache.php {{ $app_dir }}/web/app
[[ -e /tmp/object-cache.php ]] && cp /tmp/object-cache.php {{ $app_dir }}/web/app
[[ -e /tmp/advanced-cache.php ]] && rm /tmp/advanced-cache.php
[[ -e /tmp/object-cache.php ]] && rm /tmp/object-cache.php

echo 'Deployment to {{$target}} finished successfully.'
@endtask
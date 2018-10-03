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
$app_uploads_dir = $app_dir . '/web/app/uploads';

$release = 'release_' . date('YmdHis');
$servers = ['local' => '127.0.0.1', 'dev' => 'ubuntu@foody-dev.moveodevelop.com', 'medio-dev' => 'ubuntu@foody.moveodevelop.com'];

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
rm -rf assets-{{  $release }}.tar.gz
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
echo 'Installing compiled assets...'
cd ~
tar -xzf assets-{{ $release }}.tar.gz -C {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}
sudo rm -rf assets-{{ $release }}.tar.gz

cd {{ $release_dir }}/{{ $release }};


echo 'Setting permissions...'
cd {{ $release_dir }};

sudo chgrp -R www-data {{ $release }};
sudo chmod -R ug+rwx {{ $release }};

echo 'Updating symlinks...'
sudo ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }};
sudo rm -r {{ $app_uploads_dir }}
sudo ln -s {{$global_uploads_dir}} {{$app_uploads_dir}}

echo 'Deployment to {{$target}} finished successfully.'
@endtask
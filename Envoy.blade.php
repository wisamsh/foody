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

if( $target === 'prod1' || $target === 'prod2' || $target === 'prod3' || $target === 'prod4' || $target === 'prod5' || $target === 'prod6' || $target === 'prod7' || $target === 'prod8'){
    echo 'Deploy to production';
    $global_uploads_dir = $shared_drive . 'uploads';
}

$app_uploads_dir = $app_dir . '/web/app/uploads';

$servers = [
    'local' => '127.0.0.1',
    'dev' => 'ubuntu@foody-dev.moveodevelop.com',
    'mu-dev' => 'ubuntu@mu.foody-dev.moveodevelop.com',
    'medio-dev' => 'ubuntu@foody.moveodevelop.com',
    'prod1' => 'ubuntu@34.253.214.81',
    'prod2' => 'ubuntu@54.72.141.143',
    'prod3' => 'ubuntu@34.242.248.233',
    'prod4' => 'ubuntu@52.49.88.182',
    'prod5' => 'ubuntu@34.242.140.24',
    'prod6' => 'ubuntu@34.250.226.233',
    'prod7' => 'ubuntu@34.245.234.96',
    'prod8' => 'ubuntu@34.241.6.70',
    'prod-clone1' => 'ubuntu@34.254.198.248',
    'prod-clone2' => 'ubuntu@34.242.201.112'
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

mv version-hash.txt {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}/build/

sudo ln -s {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}/inc/plugins/wp-oauth-server {{ $release_dir }}/{{ $release }}/web/app/plugins/wp-oauth-server
sudo ln -s {{ $release_dir }}/{{ $release }}/{{ $theme_dir }}/inc/plugins/wordpress-social-login {{ $release_dir }}/{{ $release }}/web/app/plugins/wordpress-social-login


cd {{ $release_dir }}/{{ $release }};

echo 'Setting permissions...'
cd {{ $release_dir }};

sudo chgrp -R www-data {{ $release }};
sudo chmod -R ug+rwx {{ $release }};

cd {{ $release_dir }}/{{ $release }};

echo 'Updating symlinks...'
sudo ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }};
sudo rm -r {{ $app_uploads_dir }}
sudo ln -s {{$global_uploads_dir}} {{$app_uploads_dir}}

echo 'Deployment to {{$target}} finished successfully.'
@endtask
@setup

if(!isset($target)) {
$target = 'staging';
echo("\n\n        WARNING: No target deployment environment specified - deploying to staging by default.\n\n\n");
}

require('vendor/autoload.php');
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


$repo = 'git@github.com:MoveoTech/Foody.git';

$theme_dir = 'web/app/themes/Foody';
$release_dir = '/var/www/releases';
$db_backup_dir = '/var/www/db_backups';
$malam_home = '/var/www/';
$app_dir = '/var/www/html';

$release = 'release_' . date('YmdHis');
$servers = ['local' => '127.0.0.1', 'dev' => 'ubuntu@foody-dev.moveodevelop.com'];

@if (!isset($branch))
    $branch = 'development';
@endif

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
npm start
tar -czf assets-{{ $release }}.tar.gz dist
scp assets-{{  $release }}.tar.gz {{ $servers[$target] }}:~
rm -rf assets-{{  $release }}.tar.gz
@endtask


@task('fetch_repo', [ 'on' => $target ])
[ -d {{ $release_dir }} ] || mkdir {{ $release_dir }};
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
rm -rf assets-{{ $release }}.tar.gz

cd {{ $release_dir }}/{{ $release }};


echo 'Setting permissions...'
cd {{ $release_dir }};

chgrp -R www-data {{ $release }};
chmod -R ug+rwx {{ $release }};

echo 'Updating symlinks...'
ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }};

echo 'Deployment to {{$target}} finished successfully.'
@endtask


@story('migrate_database')
export_and_upload_database
backup_and_import_database
@endstory


@task('export_and_upload_database', ['on' => 'local'])
cd {{__DIR__}}
echo "Exporting current database..."
php -f tools/migrate-db.php export malam_wp_{{$release}}.sql
tar -czf malam_wp_{{$release}}.sql.tar.gz malam_wp_{{$release}}.sql
echo "Uploading exported database to target environment..."
scp malam_wp_{{$release}}.sql.tar.gz {{ $servers[$target] }}:~

rm -rf malam_wp_{{$release}}.sql.tar.gz
rm -rf malam_wp_{{$release}}.sql
@endtask


@task('backup_and_import_database', [ 'on' => $target ])
[ -d {{ $db_backup_dir }} ] || mkdir {{ $db_backup_dir }};

cd ~
tar -xzf malam_wp_{{$release}}.sql.tar.gz

cd {{ $app_dir }};
php -f tools/migrate-db.php export {{ $db_backup_dir }}/malam_wp_before_{{$release}}.sql
tar -czf {{ $db_backup_dir }}/malam_wp_before_{{$release}}.sql.tar.gz {{ $db_backup_dir }}/malam_wp_before_{{$release}}.sql

php -f tools/migrate-db.php clear_database
php -f tools/migrate-db.php import ~/malam_wp_{{$release}}.sql


rm -rf {{ $db_backup_dir }}/malam_wp_before_{{$release}}.sql

cd ~
rm -rf malam_wp_{{$release}}.sql.tar.gz
rm -rf malam_wp_{{$release}}.sql
@endtask
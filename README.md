# [Foody](https://foody.co.il)

The official Foody website, based on Bedrock.
Bedrock is a modern WordPress stack that helps you get started with the best development tools and project structure.
For more information on bedrock see [Bedrock](https://roots.io/bedrock/).

WordPress core updates should be done ONLY via composer (composer update johnpbloch/wordpress)

## Requirements

* PHP >= 5.6
* Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Deploys

There current deployment method is based on [Envoy by Laravel](https://laravel.com/docs/5.6/envoy):

To deploy a new version go to the project's root directory nad run
``envoy run deploy --target={target-server} --branch={git-branch-to-pull}``

Where target is one of the available servers in Envoy.blade.php.
Default is 'dev'.

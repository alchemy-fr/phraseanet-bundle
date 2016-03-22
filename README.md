# phraseanet-bundle

[![License](https://img.shields.io/packagist/l/alchemy/phraseanet-bundle.svg?style=flat-square)](https://github.com/alchemy-fr/phraseanet-bundle/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/alchemy/phraseanet-bundle.svg?style=flat-square)](https://packagist.org/packages/alchemy/phraseanet-bundle)
[![Travis](https://img.shields.io/travis/alchemy-fr/phraseanet-bundle.svg?style=flat-square)](https://travis-ci.org/alchemy-fr/phraseanet-bundle)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/alchemy-fr/phraseanet-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/alchemy-fr/phraseanet-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/)
[![Packagist](https://img.shields.io/packagist/dt/alchemy/phraseanet-bundle.svg?style=flat-square)](https://packagist.org/packages/alchemy/phraseanet-bundle/stats)

## Description

Provides a Symfony bundle for easy integration of the Phraseanet PHP SDK in your applications

## Installation

```bash
composer require alchemy/phraseanet-bundle
```

## Configuration

Using the bundle, you can define one or more Phraseanet instances. Each instance is exposed as an entity manager.

```yaml
phraseanet:
    default_instance: default
    instances:
        default:
            # Connection settings
            connection:
                client_id: PHRASEANET_APPLICATION_CLIENT_ID
                secret: PHRASEANET_APPLICATION_SECRET
                token: PHRASEANET_APPLICATION_TOKEN
                url: http://phraseanet-php55-nginx/
            # Cache settings (available cache types: redis, memcached, array, file, none)
            cache:
                type: redis
                host: localhost
                port: 6379
                validation: skip
            # Localized mappings to Phraseanet fields
            mappings:
                title:
                    fr: Titre
                    en: Title
                subtitle:
                    fr: SousTitre
                    en: SubTitle
            # Subdefinition mappings
            subdefinitions:
                low: preview
                medium: preview_X2
                high: preview_X4
            # Exposes the listed repositories in the container
            repositories:
                api.default.stories: story
                api.default.records: record
```

# phraseanet-bundle

[![Build Status](https://travis-ci.org/alchemy-fr/phraseanet-bundle.svg?branch=master)](https://travis-ci.org/alchemy-fr/phraseanet-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alchemy-fr/phraseanet-bundle/?branch=master)

## Description

Provides a Symfony bundle for easy integration of the Phraseanet PHP SDK in your applications

## Installation

```bash
composer require alchemy/phraseanet-bundle
```

## Configuration

Using the bundle, you can define one or more Phraseanet instances. Each instance is exposed as an entity manager.

```
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

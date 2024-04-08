[<img src="assets/images/komet_logo_full_bg_white.png" height="100"/>](https://projects.tib.eu/komet/en/)

# PID Manager Plugin

PID Manager for OJS

- [Citation Manager Plugin](#citation-manager-plugin)
- [Features](#features)
    - [Extract PID's](#extract-pids)
    - [Get structured metadata from external services](#get-structured-metadata-from-external-services)
    - [Task scheduler](#task-scheduler)
    - [Deposit to OpenCitations](#deposit-to-opencitations)
    - [Deposit Wikidata.org](#deposit-wikidataorg)
- [Install and configure the plugin](#install-and-configure-the-plugin)
    - [Requirements](#requirements)
    - [Install with Git](#install-with-git)
    - [Install via direct download](#install-via-direct-download)
    - [Configuration of the plugin](#configuration-of-the-plugin)
- [Development](#development)
    - [Structure](#structure)
    - [Notes](#notes)
    - [Tests](#tests)
- [Contribute](#contribute)
- [License](#license)


# Screenshot(s) / screen recording(s)

# Development

- Fork the repository
- Make your changes
- Open a PR with your changes

## Structure

    .
    ├─ assets                        # Styles, images, javascript files
    ├─ classes                       # Main folder with models / logic
    │  ├─ DataModels                 # Data models used in this plugin
    │  ├─ Db                         # Database related classes
    │  │  ├─ PluginDAO.php           # Retrieve / save data to / from database
    │  │  └─ PluginSchema.php        # Schema extestions for data models
    │  ├─ External                   # Classes for external services
    │  |  ├─ Wikidata                # Classes for Wikidata.org
    |  |  |  ├─ DataModels           # Data models for this service, e.g. mappings
    │  |  |  ├─ Api.php              # Methods for connecting to their API
    │  |  |  ├─ Inbound.php          # Methods for retrieving data
    │  |  |  └─ Outbound.php         # Methods for depositing data 
    |  |  ├─ ... Other services      # Other services follow the same structure
    |  |  ├─ ApiAbstract.php         # This class is used by service Api class
    |  |  ├─ InboundAbstract.php     # This class is used by service Inbound class
    |  |  └─ OutboundAbstract.php    # This class is used by service Outbound class
    │  ├─ FrontEnd                   # Classes for the front end, e.g. ArticleView
    │  ├─ Handlers                   # Handlers, e.g. Outbound, Inbound, API
    │  ├─ Helpers                    # Helper classes
    │  ├─ PID                        # PID classes
    │  ├─ ScheduledTasks             # Classes for the scheduler
    │  ├─ Settings                   # Settings classes
    │  └─ Workflow                   # Classes or the workflow and submission wizard
    ├─ cypress                       # Cypress tests
    ├─ docs                          # Documentation, examples
    ├─ locale                        # Language files
    ├─ templates                     # Templates folder
    ├─ tests                         # Tests folder
    │  └─ classes                    # Classes for tests
    ├─ vendor                        # Composer autoload and dependencies
    ├─ .gitignore                    # Git ignore file
    ├─ composer.json                 # Composer configuration file
    ├─ CODE_OF_CONDUCT.md            # Code of conduct
    ├─ cypress.config.js             # Cypress configuration file
    ├─ index.php                     # Entry point plugin (ojs version 3.3.0)
    ├─ LICENSE                       # License file
    ├─ PidManagerPlugin.php          # Main class of plugin
    ├─ README.md                     # This file
    ├─ package.json                  # npm packaging configuration
    ├─ scheduledTasks.xml            # Scheduler configuration file
    └─ version.xml                   # Version information of the plugin

Notes

- Autoload of the classes in the folder `classes/` is done with composer according
  to the PSR-4 specification.
- All classes have namespaces and are structured according to PSR-4 standard.
- If you add or remove classes in the `classes` folder, run the following
  command to update autoload files: `composer dump-autoload -o --no-dev`.
- Running `composer install -o --no-dev` or `composer update -o --no-dev`
  will also generate the autoload files.
- The `-o` option generates the optimised files ready for production.

## Debugging

There is a debug mode possibility in this plugin. This constant puts the plugin in debugging mode.
Extra debug information will be written to the log file (see LogHelper class)
such as API calls.
Debug information is written to the log file in the `files_dir` directory of your OJS instance.
You can find the `files_dir` constant in your config.inc.php file.

Please put the following in the file config.inc.php to enable this:
```
[pidmanager]
isDebugMode=true
```

_Careful with sensitive information, (passwords, tokens) will be written in plain text._

## Tests

**Test classes**

If you are developing, you might use the classes in `tests/classes/`.
The classes in this folder have the same folder and namespace structure as in `classes` folder.
The purpose of these classes is to override the main classes.
You can accomplish this by running the composer command `composer dump-autoload -o --dev`.
If this is done, then test or sandbox versions of API's will be used.
For example test.wikidata.org instead of www.wikidata.org.
Autoload of the classes is done with composer [classmap](https://getcomposer.org/doc/04-schema.md#classmap).  
First the classes in `tests/classes/` are loaded, after which the classes in `classes/` are loaded.
By doing this in this order, all classes present in `tests/classes/` will override the classes in `classes/`.

**Headless tests**

```bash
npm install

# start containers
npm run-script test_compose

# run tests with UI
npm run-script test_open
```

# Data models

## Metadata of OJS models

**PIDs Journal**

| name        | description                  |
|-------------|------------------------------|
| openalex_id | The OpenAlex ID of the work  |
| wikidata_id | The Wikidata QID of the work |

**PIDs Publication**

| name             | description                            |
|------------------|----------------------------------------|
| openalex_id      | The OpenAlex ID of the work            |
| wikidata_id      | The Wikidata QID of the work           |
| opencitations_id | Open Citations ID                      |
| github_issue_id  | GitHub Issue ID used by Open Citations |

# Contribute

All help is welcome: asking questions, providing documentation, testing, or even development.

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project you agree to abide by its terms.

# License

This project is published under GNU General Public License, Version 3.

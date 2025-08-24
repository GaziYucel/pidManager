[<img src="assets/images/komet_logo_full_bg_white.png" height="100"/>](https://projects.tib.eu/komet/en/)

# PID Manager Plugin

PID Manager for OJS

- [PID Manager Manager Plugin](#pid-manager-plugin)
- [Features](#features)
  - [IGSN](#igsn)
- [Screenshot(s) / screen recording(s)](#screenshots--screen-recordings)
- [Install and configure the plugin](#install-and-configure-the-plugin)
  - [Requirements](#requirements)
  - [Install with Git](#install-with-git)
  - [Install via direct download](#install-via-direct-download)
  - [Configuration of the plugin](#configuration-of-the-plugin)
- [Development](#development)
  - [Structure](#structure)
  - [Notes](#notes)
  - [Debugging](#debugging)
- [Data Models](#data-models)
- [Contribute](#contribute)
  - [How to contribute](#how-to-contribute)
- [License](#license)

[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)
[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-2.1-4baaaa.svg)](code_of_conduct.md)

# Features

## IGSN

- Manual adding sample PIDs and labels
- Allows suggesting PIDs and Labels from DataCite
- Marks samples already added with text strike through

# Screenshot(s) / screen recording(s)

![igsn-screenrecording.gif](.project/screenrecordings/igsn-screenrecording.gif)

# Install and configure the plugin

## Requirements

- PHP 8.0+

## Install with Git

Get the correct version for your OJS version:

- branch main: development version, don't use for production
- branch stable-3_4_0: use this version for OJS version 3.4.0.x
- branch stable-3_3_0: use this version for OJS version 3.3.0.x

## Install via direct download

- Download release for your OJS version. _Note the correct version for you OJS version._
- Alternatively, download the code with the option 'Download ZIP'. _Note the correct branch for your OJS version._
- Extract the downloaded file to `/plugins/generic/pidManager`.

## Configuration of the plugin

- Login in your OJS instance as an Administrator or Manager
- Navigate to Website > Plugins > Installed Plugins > Generic Plugins > PID Manager Plugin
- Activate the plugin by clicking the checkbox

# Development

## Structure

    .
    ├─ .github                       # Github specific
    ├─ .project                      # Project related files
    ├─ assets                        # Styles, images, javascript files
    ├─ Classes                       # Main folder with models / logic
    │  └─ Igsn                       # Classes for IGSN
    ├─ locale                        # Language files
    ├─ templates                     # Templates folder
    ├─ .gitignore                    # Git ignore file
    ├─ composer.json                 # Composer configuration file
    ├─ index.php                     # Only needed for OJS 3.3
    ├─ LICENSE                       # License file
    ├─ PidManagerPlugin.php          # Main class of plugin
    ├─ README.md                     # This file
    └─ version.xml                   # Version information of the plugin

## Notes

- OJS 3.3.0
  - Loading of the classes in the folder classes is done with composer classmap.
  - If you add or remove classes in this folder, run the following command to update the autoload files: composer dump-autoload -o.
  - Running composer install -o or composer update -o will also generate the autoload files.
  - The -o option generates the optimised files ready for production.
- OJS 3.4.0+
  - Loading of classes depends on PSR-4.
  - No composer.json file or vendor directory is needed.

# Data models

## IGSN

De IGSNs are saved in the publication_settings as an array of IGSN data models.

| name  | description                         |
|-------|-------------------------------------|
| doi   | Persistent identifier of the sample |
| label | Label of the sample                 |

```
[
  { 'doi': '10.58031/kiel0188gczz101', 'label': 'Sediment core SO257_1-5' },
  { 'doi': '10.58031/kiel0188gczi201', 'label': 'Sediment core SO257_6-2' },
]
```

# Contribute

All help is welcome: asking questions, providing documentation, testing, or even development.

Please note that this project is released with a [Contributor Code of Conduct](code_of_conduct.md).
By participating in this project you agree to abide by its terms.

## How to contribute

- Fork the repository
- Create a feature branch in your fork
- Make your changes
- Open a PR with your changes

# License

This project is published under GNU General Public License, Version 3.

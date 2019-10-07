# <img src="https://rawgit.com/PresentKim/SVG-files/master/plugin-icons/writecheck.svg" height="50" width="50"> WriteCheck  
__A plugin for [PMMP](https://pmmp.io) :: Write a check for use it anywhere!__  
  
[![license](https://img.shields.io/github/license/organization/WriteCheck-PMMP.svg?label=License)](LICENSE)
[![release](https://img.shields.io/github/release/organization/WriteCheck-PMMP.svg?label=Release)](../../releases/latest)
[![download](https://img.shields.io/github/downloads/organization/WriteCheck-PMMP/total.svg?label=Download)](../../releases/latest)
[![Build status](https://ci.appveyor.com/api/projects/status/xd18ryl4li9rc11m/branch/master?svg=true)](https://ci.appveyor.com/project/PresentKim/writecheck-pmmp/branch/master)
  
## What is this?   
Write Check is a plugin that make easily make check for use to anywhere.  
- [Check](https://en.wikipedia.org/wiki/Cheque) means `document that orders a bank to pay a specific amount of money from a person's account to the person in whose name the cheque has been issued`  
  
Player can make checks as freely as they have money.  
Hold the check and press the air to use the check.  
  
  
## Features  
- [x] User can make check  
  - [x] Save checks data to paper item's NBT data   
    - item id : `PAPER:0xff`  
    - nbt tag : `whitecheck-amount`  
- [x] Support configurable things  
- [x] Check that the plugin is not latest version  
  - [x] If not latest version, show latest release download url  
  
  
## Configurable things  
- [x] Configure the language for messages  
  - [x] in `{SELECTED LANG}/lang.ini` file  
  - [x] Select language in `config.yml` file  
- [x] Configure the command (include subcommands)  
  - [x] in `config.yml` file  
- [x] Configure the permission of command  
  - [x] in `config.yml` file  
- [x] Configure the whether the update is check (default "false")
  - [x] in `config.yml` file  
  
The configuration files is created when the plugin is enabled.  
The configuration files is loaded  when the plugin is enabled.  
  
  
## Command  
Main command : `/writecheck <amount> [count]`  
  
  
## Permission  
| permission            | default | description       |  
| --------------------- | ------- | ----------------- |  
| writecheck.cmd        | USER    | main command      |  

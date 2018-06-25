# <img src="./assets/icon/index.svg" height="50" width="50"> WriteCheck  
__A plugin for [PMMP](https://pmmp.io) :: Write a check for use it anywhere!__  
  
[![license](https://img.shields.io/github/license/PresentKim/WriteCheck-PMMP.svg?label=License)](LICENSE)  
[![release](https://img.shields.io/github/release/PresentKim/WriteCheck-PMMP.svg?label=Release)](https://github.com/PresentKim/WriteCheck-PMMP/releases/latest)  
[![download](https://img.shields.io/github/downloads/PresentKim/WriteCheck-PMMP/total.svg?label=Download)](https://github.com/PresentKim/WriteCheck-PMMP/releases/latest)  
  
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
- [ ] Check that the plugin is not latest version  
  - [ ] If not latest version, show latest release download url  
  
  
## Command  
Main command : `/writecheck <amount> [count]`  
  
  
## Permission  
| permission            | default | description       |  
| --------------------- | ------- | ----------------- |  
| writecheck.cmd        | USER    | main command      |  
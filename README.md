[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/192x192](meta/icon/192x192.png?raw=true)]()

[![License](https://img.shields.io/github/license/PMMPPlugin/WriteCheck.svg?label=License)](LICENSE)
[![Poggit](https://poggit.pmmp.io/ci.shield/PMMPPlugin/WriteCheck/WriteCheck)](https://poggit.pmmp.io/ci/PMMPPlugin/WriteCheck)
[![Release](https://img.shields.io/github/release/PMMPPlugin/WriteCheck.svg?label=Release)](https://github.com/PMMPPlugin/WriteCheck/releases/latest)
[![Download](https://img.shields.io/github/downloads/PMMPPlugin/WriteCheck/total.svg?label=Download)](https://github.com/PMMPPlugin/WriteCheck/releases/latest)


A plugin write check for PocketMine-MP

## Command
Main command : `/writecheck <write | lang | reload>`

| subcommand | arguments                        | description                 |
| ---------- | -------------------------------- | --------------------------- |
| Write      | \<amount\> \[count\]             | Write check                 |
| Lang       | \<language prefix\>              | Load default lang file      |
| Reload     |                                  | Reload all data             |




## Permission
| permission            | default | description       |
| --------------------- | ------- | ----------------- |
| writecheck.cmd        | USER    | main command      |
|                       |         |                   |
| writecheck.cmd.write  | USER    | write subcommand  |
| writecheck.cmd.lang   | OP      | lang subcommand   |
| writecheck.cmd.reload | OP      | reload subcommand |




## ChangeLog
### v1.0.0 [![Source](https://img.shields.io/badge/source-v1.0.0-blue.png?label=source)](https://github.com/PMMPPlugin/WriteCheck/tree/v1.0.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/WriteCheck/v1.0.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/WriteCheck/releases/v1.0.0)
- First release
  
  
---
### v1.0.1 [![Source](https://img.shields.io/badge/source-v1.0.1-blue.png?label=source)](https://github.com/PMMPPlugin/WriteCheck/tree/v1.0.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/WriteCheck/v1.0.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/WriteCheck/releases/v1.0.1)
- \[Added\] Show check usage when touch the block held check
  
  
---
### v1.1.0 [![Source](https://img.shields.io/badge/source-v1.1.0-blue.png?label=source)](https://github.com/PMMPPlugin/WriteCheck/tree/v1.1.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/WriteCheck/v1.1.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/WriteCheck/releases/v1.1.0)
- \[Changed\] translation method
- \[Removed\] save sub command
  
  
---
### v1.1.1 [![Source](https://img.shields.io/badge/source-v1.1.1-blue.png?label=source)](https://github.com/PMMPPlugin/WriteCheck/tree/v1.1.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/WriteCheck/v1.1.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/WriteCheck/releases/v1.1.1)
- \[Fixed\] Violation of PSR-0
- \[Fixed\] Add api 3.0.0-ALPHA11
- \[Changed\] Show only subcommands that sender have permission to use
- \[Changed\] Add return type hint
- \[Changed\] Rename main class to WriteCheck
- \[Added\] Add PluginCommand getter and setter
- \[Added\] Add getters and setters to SubCommand
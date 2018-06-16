[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

<img src="./assets/icon/index.svg" height="256" width="256">  

[![License](https://img.shields.io/github/license/PresentKim/WriteCheck.svg?label=License)](LICENSE)
[![Release](https://img.shields.io/github/release/PresentKim/WriteCheck.svg?label=Release)](https://github.com/PresentKim/WriteCheck/releases/latest)
[![Download](https://img.shields.io/github/downloads/PresentKim/WriteCheck/total.svg?label=Download)](https://github.com/PresentKim/WriteCheck/releases/latest)


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
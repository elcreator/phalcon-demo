# phalcon-demo
Basic app that I created to demonstrate capabilities of the Phalcon framework (v5, currently beta3).
Implemented
- https
- localization
- static page serving
- API
- basic ACL (admin, user, quest)
- user registration and login
- profile
- admin panel

Image based on Ubuntu 20.

Vagrantfile contains scripts that automatically installs
- Nginx
- PHP 7.4
- Phalcon 5 alpha
- MySQL8
- XDebug
- and minor handy or required tools

to the VM.

## Installation
Install Vagrant https://www.vagrantup.com/downloads

`vagrant up`

Add

`192.168.56.37 project.local`

to your /etc/hosts file.

Open https://project.local in your browser. Type `thisisunsafe` on the Chrome error page if Chrome blocks self-signed certificate.

Use `vagrant ssh` to use sudo.

Use `ssh://127.0.0.1:2222` login: `project` password: `project` for regular tasks like the code deployment.

DB can be accessed via `jdbc:mysql://192.168.56.37:3306/project` login: `project` password: `project`

It's seeded with the next user records:

admin@project.local / Password

tester1@project.local / tester1

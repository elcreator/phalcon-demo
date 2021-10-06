# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/focal64"
  config.vm.provider "virtualbox" do |v|
    v.memory = 4096
    v.cpus = 4
  end
  config.vm.network "private_network", ip: "192.168.56.37"
  config.vm.synced_folder ".", "/vagrant", disabled: false, automount: true, SharedFoldersEnableSymlinksCreate: false
  config.vm.provision "shell", path: "install.sh"
end

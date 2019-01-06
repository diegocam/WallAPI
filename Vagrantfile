# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrant Box Settings

box         = 'bento/ubuntu-18.04'  # Vagrant box to use
ip          = '192.168.50.5'        # VM Server IP Address
hostname    = 'wall-api.local'      # VM Server Hostname
vboxname    = 'Wall API'            # VB Name
provisioner = 'VagrantProvision.sh' # Path to provisioning script

Vagrant.configure("2") do |config|

  # Box
  config.vm.box = box
  config.vm.hostname = hostname

  config.vm.provider "virtualbox" do |p|
    p.name = vboxname
    p.customize ["modifyvm", :id, "--memory", 2048]
  end

  # Network
  config.vm.network "private_network", ip: ip
  config.vm.network :forwarded_port, guest: 80, host: 8080, auto_correct: true

  # Folder Sync
  config.vm.synced_folder ".", "/var/www", id: "vagrant", type: "nfs", mount_options: ['nolock', 'vers=3', 'tcp', 'fsc', 'rw', 'noatime', 'actimeo=1']

  # Misc
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  # Provisioning
  config.vm.provision "shell" do |s|
    s.path = provisioner
  end

end

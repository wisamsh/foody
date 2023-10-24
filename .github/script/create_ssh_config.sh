#!/bin/sh

# Write contents of key file
mkdir -p ~/.ssh
echo "$SSH_KEY" > ~/.ssh/foody.pem
chmod 600 ~/.ssh/foody.pem

# Create the ssh config file with the specified content
cat <<EOL > ~/.ssh/config
Host *
  AddKeysToAgent yes
  UseKeychain yes
  IdentityFile ~/.ssh/foody.pem

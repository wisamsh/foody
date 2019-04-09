#!/usr/bin/env bash
env=$1

if  [[ -z "$1" ]]
    then
    echo "no env set"
    exit 0
fi

cp ".env.${env}" .env
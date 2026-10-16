#!/bin/sh

set -eu

repository=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
fixture=$(mktemp -d)
trap 'rm -rf "$fixture"' EXIT HUP INT TERM

mkdir -p "$fixture/app/storage" "$fixture/outside"
touch "$fixture/app/storage/upload" "$fixture/app/.env" "$fixture/app/executable" "$fixture/outside/untouched"
chmod 777 "$fixture/app" "$fixture/app/storage" "$fixture/app/storage/upload" "$fixture/outside/untouched"
chmod 600 "$fixture/app/.env"
chmod 755 "$fixture/app/executable"
ln -s "$fixture/outside" "$fixture/app/external"

cd "$fixture/app"
sh "$repository/scripts/restrict-deploy-permissions.sh"

[ "$(stat -c %a .)" = 755 ]
[ "$(stat -c %a storage)" = 755 ]
[ "$(stat -c %a storage/upload)" = 755 ]
[ "$(stat -c %a .env)" = 600 ]
[ "$(stat -c %a executable)" = 755 ]
[ "$(stat -c %a "$fixture/outside/untouched")" = 777 ]
[ -w storage/upload ]
[ -x executable ]

echo 'Deployment permissions passed'

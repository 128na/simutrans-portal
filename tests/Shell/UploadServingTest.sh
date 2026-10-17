#!/bin/sh

set -eu

repository=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
source_path="$repository/public/.htaccess"
if command -v cygpath >/dev/null 2>&1; then
    source_path=$(cygpath -w "$source_path")
fi
container=$(docker run -d --rm -p 127.0.0.1::80 httpd:2.4-alpine@sha256:3440c39d8d6f54fa9ad2549e5a60c19ddd435faadc29c1ad28aa795f71888889)
trap 'docker rm -f "$container" >/dev/null' EXIT HUP INT TERM
docker cp "$source_path" "$container:/usr/local/apache2/htdocs/.htaccess" >/dev/null
docker exec -i "$container" sh <<'EOF'
set -eu
sed -i 's/^#LoadModule rewrite_module/LoadModule rewrite_module/; s/^#LoadModule headers_module/LoadModule headers_module/; s/AllowOverride None/AllowOverride All/g' /usr/local/apache2/conf/httpd.conf
mkdir /usr/local/apache2/uploads
ln -s /usr/local/apache2/uploads /usr/local/apache2/htdocs/storage
for name in sample.html sample.txt sample.pak sample.png sample.JPG script.php script.PHP script.php.txt script.phtml; do
    printf 'harmless test fixture' > "/usr/local/apache2/uploads/$name"
done
printf 'health' > /usr/local/apache2/htdocs/health.txt
EOF
docker restart "$container" >/dev/null
port=$(docker port "$container" 80/tcp | sed 's/.*://')
base="http://127.0.0.1:$port"
curl --retry 10 --retry-all-errors --retry-delay 1 --max-time 5 -fsS "$base/health.txt" >/dev/null

for path in script.php script.PHP script.php.txt script.phtml script.php/path script.%70hp; do
    status=$(curl -sS -o /dev/null -w '%{http_code}' "$base/storage/$path")
    [ "$status" = 403 ] || { echo "Expected 403: $path ($status)"; exit 1; }
done

for path in sample.html sample.txt sample.pak sample.png sample.JPG 'sample.png?x=1'; do
    headers=$(curl -fsS -D - -o /dev/null "$base/storage/$path" | tr -d '\r')
    printf '%s\n' "$headers" | grep -qi '^X-Content-Type-Options: nosniff$'
    printf '%s\n' "$headers" | grep -qi "^Content-Security-Policy: sandbox; default-src 'none'$"
    case "$path" in
        *.png|*.JPG|*.png\?*)
            if printf '%s\n' "$headers" | grep -qi '^Content-Disposition:'; then
                echo "Image unexpectedly forced to download: $path"; exit 1
            fi
            printf '%s\n' "$headers" | grep -qi '^Content-Type: image/'
            ;;
        *) printf '%s\n' "$headers" | grep -qi '^Content-Disposition: attachment$' ;;
    esac
done

headers=$(curl -fsS -D - -o /dev/null "$base/%73torage/sample.html" | tr -d '\r')
printf '%s\n' "$headers" | grep -qi '^Content-Disposition: attachment$'
printf '%s\n' "$headers" | grep -qi "^Content-Security-Policy: sandbox; default-src 'none'$"

headers=$(curl -fsS -D - -o /dev/null "$base/health.txt")
if printf '%s\n' "$headers" | grep -qiE '^Content-(Security-Policy|Disposition):'; then
    echo 'Upload headers escaped the storage scope'; exit 1
fi
echo 'Upload serving protections passed'

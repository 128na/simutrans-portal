#!/bin/sh

set -eu

# Run from the deployment root; find does not follow symbolic links.
find . -type d -exec chmod go-w {} +
find . -type f -exec chmod go-w {} +

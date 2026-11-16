#!/bin/bash

# copies the real project files into the web demo, so the demo always runs the same code.
# run it after changing any php file:   bash tools/build-demo.sh
#
# the files get a .txt on the end, so any static server sends them as plain text
# (php -S would try to run them otherwise). the demo page removes the .txt again in the php file system

cd "$(dirname "$0")/.."

mkdir -p docs/demo/app
rm -f docs/demo/app/*

for f in *.php style.css
do
    cp "$f" "docs/demo/app/$f.txt"
done

( cd docs/demo/app && ls *.txt | sed 's/\.txt$//' | sed 's/.*/"&"/' | paste -sd, | sed 's/^/[/;s/$/]/' > manifest.json )

echo "demo files updated:"
cat docs/demo/app/manifest.json

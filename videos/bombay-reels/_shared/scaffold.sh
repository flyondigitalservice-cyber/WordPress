#!/bin/bash
# usage: scaffold.sh vNN "Title" [work images...]
set -e
ROOT=$(cd "$(dirname "$0")/.." && pwd); V=$1; shift; TITLE=$1; shift
D=$ROOT/$V; mkdir -p $D/assets/vo $D/assets/work $D/renders
cp $ROOT/_shared/template/hyperframes.json $ROOT/_shared/template/package.json $D/
printf '{"id":"bombay-%s","name":"Bombay Marketing %s — %s"}\n' $V $V "$TITLE" > $D/meta.json
cp -r $ROOT/_shared/kit/brand $ROOT/_shared/kit/fonts $ROOT/_shared/kit/sfx $ROOT/_shared/kit/brand.css $ROOT/_shared/kit/kit.js $D/assets/
cp $ROOT/_shared/vo/${V}_*.wav $D/assets/vo/
for w in "$@"; do cp $ROOT/_brand/raw/$w $D/assets/work/; done
echo scaffolded $D

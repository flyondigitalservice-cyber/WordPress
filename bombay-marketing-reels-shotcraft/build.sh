#!/bin/sh
# Build reels:  sh build.sh            (06–12)        sh build.sh 6 9       (only those)
# Each reel is rendered twice from the same timeline: with the music bed, and without it (voice + whoosh) for adding your own audio.
set -e
cd "$(dirname "$0")"; mkdir -p reels out
node tools/build-data.mjs >/dev/null; python3 tools/voice.py >/dev/null
[ -f public/audio/whoosh.wav ] || python3 tools/whoosh.py
[ $# -gt 0 ] && SEL="$*" || SEL="6 7 8 9 10 11 12"
echo '{"bgm":false}' > out/props-nobgm.json
master() { # loudness to -14 LUFS, true peak -1.5 dB; picture untouched
  ffmpeg -loglevel error -y -i "$1" -c:v copy -af "loudnorm=I=-14:TP=-1.5:LRA=11" -c:a aac -b:a 192k -ar 48000 -movflags +faststart "$2"
}
for n in $SEL; do
  nn=$(printf %02d "$n"); id=$(node -e "console.log(require('./src/data/reels.json').find(r=>r.n===$n).id)")
  npx remotion render src/index.ts "Reel$nn" "out/$id.mp4" --codec=h264 --crf=18 --concurrency=4 --log=error
  master "out/$id.mp4" "reels/$id.mp4"
  npx remotion render src/index.ts "Reel$nn" "out/$id-nomusic.mp4" --props=out/props-nobgm.json --codec=h264 --crf=18 --concurrency=4 --log=error
  master "out/$id-nomusic.mp4" "reels/$id-nomusic.mp4"
  frames=$(ffprobe -v error -select_streams v:0 -show_entries stream=nb_frames -of csv=p=0 "reels/$id.mp4")
  ffmpeg -loglevel error -y -ss "$(node -e "console.log((($frames-20)/30).toFixed(2))")" -i "reels/$id.mp4" -frames:v 1 -q:v 2 "reels/$id.jpg"
  echo "done $id"
done

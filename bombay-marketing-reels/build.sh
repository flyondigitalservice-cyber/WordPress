#!/bin/sh
# Build the campaign:  sh build.sh            (all 12 reels)
#                      sh build.sh 3 7        (only reels 3 and 7)
# Needs the Lemo-Opuscar library (core + voice + music tiers). Voice lines are cached in voice/; delete a reel's folder to re-record it.
set -e
HERE=$(cd "$(dirname "$0")" && pwd)
LIB=${LIB:-$HOME/lemo-opuscar}
export LIB
[ -f "$LIB/core/render/video.mjs" ] || { echo "set LIB to the library folder"; exit 1; }
PY="$LIB/.venv/bin/python"; FPS=60
cd "$HERE"; node tools/lines.mjs >/dev/null; node tools/meta.mjs
[ $# -gt 0 ] && SEL="$*" || SEL="1 2 3 4 5 6 7 8 9 10 11 12"
cd "$LIB"
for n in $SEL; do
  id=$(sed -n "${n}p" "$HERE/out/ids.txt"); O="$HERE/out/$id"; mkdir -p "$O" "$HERE/reels"
  [ -f "$HERE/voice/$id/dur.json" ] || "$PY" "$HERE/tools/tts.py" "$LIB" "$HERE/voice/$id/lines.json" "$HERE/voice/$id"
  node core/render/events.mjs "$HERE" --q "reel=$n" --size 1080x1920 --out "$O/events.json"
  "$PY" "$HERE/tools/srt.py" "$O/events.json" "$HERE/reels/$id.srt"
  "$PY" "$HERE/mix.py" "$id"
  node core/render/video.mjs "$HERE" --q "reel=$n" --size 1080x1920 --fps $FPS --workers 4 --out "$O/video.mp4"
  sh "$HERE/tools/master.sh" "$O/video.mp4" "$O/mix.wav" "$HERE/reels/$id.mp4"
  node core/render/still.mjs "$HERE" 999 --q "reel=$n" --size 1080x1920 --out "$O/poster" >/dev/null && mv "$O/poster/t_999.jpg" "$HERE/reels/$id.jpg"
done

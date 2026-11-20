#!/bin/sh
# One command per film:  sh build.sh <film folder>     (VOICE=1 regenerates the narration)
set -e
LIB=${LIB:-/root/lemo-opuscar}; export LIB
HERE=$(cd "$(dirname "$0")" && pwd)            # .../brand-reels/shared
FILM=$(cd "$1" && pwd); NAME=$(basename "$FILM")
PY="$LIB/.venv/bin/python"
export PATH="${FFBIN:+$FFBIN:}$PATH"
export LEMO_ANGLE=${LEMO_ANGLE:-swiftshader}
rm -rf "$FILM/kit" && cp -r "$HERE" "$FILM/kit"
if [ "${VOICE:-0}" = "1" ] || [ ! -f "$FILM/out/voice/dur.json" ]; then "$PY" "$LIB/core/tts/tts.py" "$FILM/voice/lines.json" "$FILM/out/voice"; fi
"$PY" "$HERE/hi_check.py" "$FILM/voice/lines.json" "$FILM/out/voice" > "$FILM/out/voicecheck.txt" || { cat "$FILM/out/voicecheck.txt"; echo "voice check: some lines low, listen"; }
"$PY" "$HERE/timeline.py" "$FILM"
cd "$LIB"
node core/render/events.mjs "$FILM" --size 1080x1920
node core/render/video.mjs "$FILM" --fps 24 --workers ${WORKERS:-4} --size 1080x1920 --out "$FILM/out/video.mp4"
"$PY" "$HERE/mix.py" "$FILM"
sh core/render/mux.sh "$FILM/out/video.mp4" "$FILM/out/mix.wav" "$FILM/$NAME.mp4" 24 1
node core/render/readcheck.mjs "$FILM" --size 1080x1920 > "$FILM/out/readcheck.txt" && echo "readcheck ok" || tail -15 "$FILM/out/readcheck.txt"
node core/render/still.mjs "$FILM" ${POSTER_T:-6} --size 1080x1920 --q nosubs=1 --out "$FILM/out/frame" --prefix p_ >/dev/null
ffmpeg -v error -y -i "$FILM/out/frame/p_${POSTER_T:-6}.jpg" -q:v 3 "$FILM/poster.jpg"
echo "done: $FILM/$NAME.mp4"

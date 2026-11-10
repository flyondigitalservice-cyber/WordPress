#!/bin/sh
# master.sh video.mp4 mix.wav out.mp4 — two-pass loudnorm to −14 LUFS / −1.5 dBTP, H.264 + AAC for Instagram/YouTube Shorts
set -e
V="$1"; A="$2"; O="$3"
M=$(ffmpeg -hide_banner -i "$A" -af loudnorm=I=-14:TP=-1.5:LRA=11:print_format=json -f null - 2>&1 | tail -12)
g() { echo "$M" | grep "\"$1\"" | sed 's/[^0-9.-]//g'; }
ffmpeg -hide_banner -loglevel error -y -i "$V" -i "$A" -map 0:v -map 1:a \
  -af "loudnorm=I=-14:TP=-1.5:LRA=11:measured_I=$(g input_i):measured_TP=$(g input_tp):measured_LRA=$(g input_lra):measured_thresh=$(g input_thresh):offset=$(g target_offset):linear=true,apad" \
  -c:v libx264 -preset medium -crf 23 -pix_fmt yuv420p -colorspace bt709 -color_primaries bt709 -color_trc bt709 -color_range tv -vf "scale=in_range=full:out_range=tv,format=yuv420p" -movflags +faststart \
  -c:a aac -b:a 192k -ar 48000 -shortest "$O.tmp.mp4"
mv "$O.tmp.mp4" "$O"; echo "$O"

#!/bin/bash
cd "$(dirname "$0")/.."
H="node /root/.claude/skills/hyperframes/scripts/plugin-cli.mjs"
r(){ v=$1; slug=$2; ct=$3; title=$4; (cd $v && $H render . -q high -o renders/$v-$slug.mp4 >/tmp/claude-0/render-$v.log 2>&1) && _shared/finalize.sh $v $slug $ct "$title" > /tmp/claude-0/final-$v.log 2>&1; echo "$v done $?"; }
r v01 we-are-bombay-marketing 1.8 "We Are Bombay Marketing" & r v02 your-website-is-your-digital-storefront 1.6 "Your Website Is Your Digital Storefront" & r v03 be-found-be-chosen 1.6 "Be Found. Be Chosen." & wait
r v04 own-your-local-presence 1.8 "Own Your Local Presence" & r v05 attention-is-the-new-currency 1.4 "Attention Is the New Currency" & r v06 turn-clicks-into-customers 1.6 "Turn Clicks Into Customers" & wait
r v07 make-your-brand-unforgettable 1.6 "Make Your Brand Unforgettable" & r v08 ideas-into-impact 2.0 "Ideas Into Impact" & r v09 lets-build-something-bigger 1.5 "Let's Build Something Bigger" & wait
echo ALL DONE

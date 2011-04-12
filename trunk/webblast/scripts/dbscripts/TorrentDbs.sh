#!/bin/bash
ulimit -n 90000
rm -f /state/partition1/ncbi_archives/*.torrent
cp -f /home/blastdbs/ncbi_archive/*.torrent /state/partition1/ncbi_archives/
/home/blastdbs/btlaunchmanynevo.py /state/partition1/ncbi_archives --max_upload_rate 0 --display_interval 10 --max_uploads 1

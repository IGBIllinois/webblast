#!/bin/bash
ulimit -n 90000
rm -f dstate
/export/home/blastdbs/bittorrent/bttrackNevo.py --port 6969 --dfile dstate --show_names 1 --allowed_dir /export/home/blastdbs/ncbi_archive &
./btlaunchmanynevo.py /export/home/blastdbs/ncbi_archive --max_upload_rate 0 --display_interval 60 --max_uploads 1
pkill bttrackNevo.py

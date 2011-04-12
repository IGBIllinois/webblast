#!/bin/bash

qsub -pe serial 8 -v PYTHONPATH=/home/blastdbs/python2.4/site-packages/ /export/home/blastdbs/TorrentDbs.sh


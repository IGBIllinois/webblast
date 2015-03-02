#!/bin/bash
updatefolder=`ls /home/mirrors/NCBI/BLAST_DBS/ | sort | egrep '^201' | tail -n 1`
echo $updatefolder
rm /home/a-m/blastweb/DATABASES/NCBI
ln -sf /home/mirrors/NCBI/BLAST_DBS/$updatefolder /home/a-m/blastweb/DATABASES/NCBI

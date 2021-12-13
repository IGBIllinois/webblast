# Web Blast

* Web interface to submit blast jobs to a cluster.  It splits the blast queries into chunks and distributes them across a cluster.

## Create database with file larger than 2GB ##

* copy the fasta file to the /export/home/blastdbs/userdbs
* then run the formatdb command
* If the database is a protein database then set -p parameter to T if it is a nucleotide then set -p to F
```
/opt/Bio/ncbi/bin/formatdb -i /export/home/blastdbs/userdbs/database_name.fasta -p T -o F -a F -v 1000 -b T -e F -s F -t "database_name"
```
* Delete the fasta file you copied over.
* Run the command to sync the user dbs to the nodes
```
/var/www/html/webblast/script/runUpdateUserDbs.pl
```
* Log into webblast admin page [http://clcluster.igb.uiuc.edu/admin.php http://clcluster.igb.uiuc.edu/admin.php] go to the databases tab
* Fill in the Add user database manually with the database name and type of database you updated above

# Installation 

## Setup Web Interface

* Copy the webblast website to the web visible folder

### Setup Scheduler ###

* Create a user **blastweb** on the cluster which will submit the webblast jobs.
* Copy the **blastweb** folder to the blastweb user's home directory
* Folder structure:
```
/home/a-m/blastweb
|-- DATA
|   |-- delete #data set for deletion
|   |-- finalized #fully concatenated result files ready for download 
|   |-- input_chunks #chunked input fasta file named after database query row ids
|   |-- output_chunks #chunked results
|   |-- output_csv_chunks #chunked custom csv formatted chunked results
|   `-- uploads #user uploaded fasta queries prechunked state
|-- DATABASES
|   `-- USER #user custom databases created via web form
|   `-- NCBI #NCBI symlink to mirror on biocluster for blast databases
|-- head_cron
|   |-- headcron.sh #crontab ready script for scheduler
|   |-- headcron_updatedb.sh #crontab ready script for updating blast dbs
|   |-- head_node_cron.pl #perl script to run scheduler
|-- slave_scripts
|   |-- slave_script.pl #worker script actually run blastall on chunks
|   `-- SubmitBlastWebNode.sh #submission script
`-- webblast.conf #Centralized ini formatted configuration script for both website and scheduler
 -- updatencbi.sh #script to update the NCBI symlink under DATA directory when no jobs are running
```
### Permissions

* Apache user permissions
* **DATA'** folder - read/write
* **webblast.conf** - read
* **DATABASES/USER** - read/write

### Crontab
* Set crontab commands to run scheduler every 5 minutes:
```
#CRONTAB COMMAND RUN WEBBLAST SCHEDULER
*/5 * * * * blastweb /home/a-m/blastweb/head_cron/headcron.sh >> /home/a-m/blastweb/head_cron.log 2>&1
```

* Set crontab to update the database
```
#CRONTAB COMMAND RUN WEBBLAST DATABASE UPDATE
0 22 * * * blastweb /home/a-m/blastweb/head_cron/headcron_updatedb.sh >> /home/a-m/blastweb/head_cron.log 2>&1
```


## Potential Problems

* Server crashes while in the **Node Status** section on the Administration page a node has status **submitting**. You will need to manually change that column in the MySQL database to 5 (UPDATE nodes SET status=5).
* If you used **qdel** to remove running jobs manually for what ever emergency, you will now need to change the blast_queries status on the jobs that ran from **running** to **new** (UPDATE blast_queries SET statusid = 1 WHERE statusid=2). Make sure the scheduler in the administration page is set to 0 nodes and the slave nodes are not running any slaveloop.pl scripts first.


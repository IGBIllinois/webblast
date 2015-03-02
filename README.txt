SETUP:
* Create a user blastweb on the cluster which will submit the webblast jobs
* Copy the webblast website to the web visible folder
* Copy blastweb folder the blastweb user's home directory
* Create the "DATA" folder in the blastweb user's home directory
** "DATA" will be where the submitted blast jobs chunks and restuls will be stored
** in the DATA directory create the following folders
	*** "delete" - the folder where deleted items will be moved in order to be deleted
	*** "finalize" - the finished concatinated results
	*** "input_chunks" - folder where the user's quries will be stored chunked and numbered according to database row ids
	*** "output_chunks" - folder where chunk results will be store name according to database row ids
	*** "output_csv_chunk" - custom CSV format of chunk results name after database row ids
	*** "uploads" - folder where the uploaded user fasta file will be stored while it's being chunked into the "input_chunks" folder by the chunking script
* Create "DATABASES" folder in blastweb user's directory
** In the "DATABASES" folder create the folder "USER"
	"USER" - this is where user's custom databases are stored, these databases are created via a webform from a fasta file.
* Copy the "webblast.conf" file the blastweb's user's home directory
**
*Set crontab commands:
* This command will schedule workers to run 
<pre>
#CRONTAB COMMAND RUN WEBBLAST SCHEDULER
*/5 * * * * blastweb /home/a-m/blastweb/head_cron/headcron.sh >> /home/a-m/blastweb/head_cron.log 2>&1
</pre>

<pre>
#CRONTAB COMMAND RUN WEBBLAST DATABASE UPDATE
0 22 * * * blastweb /home/a-m/blastweb/head_cron/headcron_updatedb.sh >> /home/a-m/blastweb/head_cron.log 2>&1
</pre>
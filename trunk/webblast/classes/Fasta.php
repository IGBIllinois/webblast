<?php

/**
 *  fasta_parser_class.php (renamed to Fasta for GenePHP compatability)
 *
 * v0.0.01 - 15-March-2003
 *
 * v0.0.1 - 5-May-2003 - converted to class for GenePHP import module
 *
 * For iterating through a file(handle) or array of lines, returns
 * an array for each record
 *
 * Initially written and maintained by Sean M. Clark
 * Consider this licensed under the GPL v2.0.
 *
 * @package biophp
 * @author Sean Clark
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://bioinformatics.org/biophp 
 * - a "BioPHP" project
*/

/**
 * Support class for seqIOimport class.  Parses Fasta files
 * 
 * @package biophp
 * @author Sean Clark
 */ 
class Fasta
{
    var $source; //might be a string, or file handle(resource)
    var $next_label; //contents of just-read label
    var $source_lines=Array(); //if source is a string, this holds its lines
    var $sourcetype; //tracks whether we need to close filehandle ourselves

//################class constructor###################

    /**
     * Parse_Fasta constructor
     */
    function Fasta(&$source) {
        if($source != "") {
            $this->setSource($source);
        }
    }

//###############Get/Set functions##################

    /** 
     * Return the next sequence record as an array,
     * @return Array
     */
    function fetchNext()
    {
        $record=Array(); //for returning results
        if($this->next_label) {
            list($id,$sequence)=$this->findNextLabel();
            //treat the defline used at NCBI
            //>gi|42794768|ref|NM_002576.3| Homo sapiens p21/Cdc42/Rac1-activated kinase 1 (STE20 homolog, yeast) (PAK1), mRNA
            if(ereg("^gi", $id)) {
                genbankDefline($id, $record);
            }
            elseif(ereg("^ref", $id)) {
                refseqDefline($id, $record);
            }
            else {
            $record['id']=$id;
            }
            $record['sequence']=$sequence;
            return $record;
        }
        else {
            //no more records in file
            return false;
        }
    }


    /**
     * <p>Determines whether the source is a file or a string</p>
     *  Mainly for checking whether or not you've accidentally
     * passed an invalid filename
     */
    function isFromFile() {
        if(is_resource($this->source)) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * <p>Declare or change the source data</p>
     * Can be an actual string containing data, a file handle, or
     * a filename.
     */
    function setSource(& $source) {
        if(is_resource($source)) {
            $this->source=$source;
            $this->sourcetype='resource';
            $this->readToLabel(); //find first label

        }
        elseif(is_array($source)) {
            //assume an already-split array of lines
            $this->sourcetype='text';
            $this->source_lines=$source;
        }
        elseif(@file_exists($source)) {
            //if passed a filename, opens it
            $this->sourcetype='file';
            $this->source=fopen($source,"r");
            $this->readToLabel();
        }
        else {
        // assume source is a string containing data
            $this->sourcetype='text';
            $this->source_lines=preg_split("/[\r\n]/",$source);
        }
    }


//################"Internal" functions#############

    function findNextLabel()
    {
        // a "wrapper" - calls the appropriate function for string or file
        if($this->isFromFile()) {
            return $this->findNextInFile();
        }
        else {
            return $this->findNextInString();
        }
    }


    /**
     * <p>Read to next label or end of file</p>
     * Return an array with label and
     * sequence,  or false if no more records
     */
    function findNextInFile()
    {
        $sequencedata="";  //hold collected sequence lines
        $label=$this->next_label;
        while(!(preg_match("/^>/",$line=fgets($this->source,1024)) || feof($this->source))) {
            //keep adding remaining lines up until end of file or next label
            $sequencedata.=trim($line);
        }
        if(!(feof($this->source))) {
            $this->next_label=trim(substr($line,1)); //remove the leading > character
        }
        else {
            if ($this->sourcetype=='file') {
                fclose($this->source);
            }

            $this->next_label=false;
        }
        return Array($label,$sequencedata);
    }


    /** 
     * <p>Pulls the next sequence from the string array</p>
     * Returns an array with label,sequence
     */
    function findNextInString()
    {
        $label=$this->next_label;
        while ((!preg_match("/^>".$label."/",$line=array_shift($this->source_lines))) && (count($this->source_lines) > 0) ) {
            //keep adding remaining lines up until end of file or next FASTA label
            $sequencedata.=trim($line);
        }
        if(count($this->source_lines) > 0) {
            $this->next_label=trim(substr($line,1)); //remove the leading > character
        }
        else {
            $this->next_label=false;
        }
        return Array($label,$sequencedata);
    }


    /**
     * "wrapper" - calls appropriate function type
     */
    function readToLabel($label=".*")
    {
        if($this->isFromFile()) {
            return $this->readFileToLabel($label);
        }
        else {
            return $this->readStringToLabel($label);
        }
    }


    /**
     * scans to next label by default, or to the label name specified
     */
    function readFileToLabel($label=".*")
    {
        while(!(preg_match("/^>".$label."/",$line=fgets($this->source,1024)) || feof($this->source))) {
             //do nothing, really...just scan until that point
        }
        if(feof($this->source)) {
            $this->next_label=false;
            if($this->sourcetype=='file') {
                fclose($this->source);
            }
        }
        else {
            $this->next_label=trim(substr($line,1));
        }
    }


    /**
     * Advances through the source array to the specified label
     */
    function readStringToLabel($label=".*")
    {
        while(!(preg_match("/^>".$label."/",$line=array_shift($this->source_lines))) && (count($this->source_lines) > 0)) {
         //do nothing, really...just scan until that point
        }
        if(count($this->source_lines) < 1) {
            $this->next_label=false;
        }
        else {
            $this->next_label=trim(substr($line,1));
        }
    }

}


    /**
     * Split the genbank defline to get more infos from the sequence
     */
    function genbankDefline($id, &$record)
    {
        $id = substr($id, 3); //take off gi|
        list($id, $db, $accession_version, $definition) = explode("|", $id);
        list($accession, $version) = explode(".", $accession_version);
        $record['ncbi_gi_id']   =$id;
        $record['id']           =$accession;
        $record['accession']    =$accession;
        $record['version']      =$accession_version;
        $record['definition']   =$definition;
        if (ereg("mRNA", $definition)) $record['moltype'] = "mRNA";
        if (ereg("Homo sapiens", $definition)) {
            $record['source']   = "Homo sapiens (human)";
            $record['organism'] = "Homo sapiens";
        }
        
    }

    /**
     * Split the Refseq defline to get more infos from the sequence
     */
    function refseqDefline($id, &$record)
    {
        $id = substr($id, 4); //take off ref|
        list($accession_version, $definition) = explode("|", $id);
        list($accession, $version) = explode(".", $accession_version);
        $record['accession']    =$accession;
        $record['version']      =$accession_version;
        $record['definition']   =$definition;
        if (ereg("mRNA", $definition)) $record['moltype'] = "mRNA";
        if (ereg("Homo sapiens", $definition)) {
            $record['source']   = "Homo sapiens (human)";
            $record['organism'] = "Homo sapiens";
        }

        
    }










?>


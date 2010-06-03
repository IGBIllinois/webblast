<FORM ACTION=/webblast/index.php METHOD = POST NAME="MainBlastForm" ENCTYPE= "multipart/form-data">

<TABLE BORDER=1>
<TR>
<TD>
<H1><B>IGB BLAST</B></H1>
</TD>
<TD>
</TD>
</TR>
<TR>
<TD>
Job Name:
</TD>
<TD>
<INPUT TYPE="TEXT" NAME="JOB_NAME">
<?php echo $errorJobName; ?>
</TD>
</TR>
<TR>
<TD>
Program:
</TD>
<TD>
<select name = "PROGRAM">
<?php
$blastsArr=$sqlDataBase->query("SELECT * FROM blasts");
foreach($blastsArr as $row)
{
	extract($row);
	echo "<option value=".$id.">".$name."</option>";
}


?>
</select>
<?php echo $errorProgram; ?>
</TD>
</TR>
<TR>
<TD>
	Algorithm (blastp):
</TD>
<TD>
	<input name="BLAST_PROGRAMS" type="radio" value="blastp" checked="checked" />blastp (protein-protein BLAST)<br>
	<input name="BLAST_PROGRAMS" type="radio" value="psiBlast"   />PSI-BLAST (Position-Specific Iterated BLAST<br>
	<input name="BLAST_PROGRAMS" type="radio" value="phiBlast"  />PHI-BLAST (Pattern Hit Initiated BLAST)
<?php echo $errorAlgorithm; ?>
</TD>
</TR>
<TR>
<TD>
	Optimize For(blastn):
</TD>
<TD>
	<input name="BLAST_PROGRAMS"  type="radio" value="megaBlast" checked="checked" /> Highly similar sequences (megablast)<br>
	<input name="BLAST_PROGRAMS"  type="radio" value="discoMegablast"  /> More dissimilar sequences (discontiguous megablast)<br>
	<input name="BLAST_PROGRAMS"  type="radio" value="blastn"  /> Somewhat similar sequences (blastn)
<?php echo $errorOptimizeFor; ?>
</TD>
</TR>
<TR>
<TD>
<BR>
FASTA file to use: 
</TD>
<TD>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="10000000000">
<INPUT TYPE="file" NAME="SEQFILE">
<?php echo $errorSeqFile; ?>
</TD>
</TR>
<TR>
<TD>
Filter:
</TD>
<TD>
<INPUT TYPE="checkbox" VALUE="L" NAME="LOW_COMPLEXITY" CHECKED> Low complexity<br>
<input type="checkbox" name="FILTER" value="R" />
Species-specific repeats for:              
<select id="repeats" name="REPEATS">
	<option value="repeat_9606">Human</option>
	<option value="repeat_9989">Rodents</option>
	<option value="repeat_3702"  >Arabidopsis</option>
	<option value="repeat_4530"  >Rice</option>
	<option value="repeat_40674"  >Mammals</option>
	<option value="repeat_4751"  >Fungi</option>
	<option value="repeat_6239"  >C. elegans</option>
	<option value="repeat_7165"  >A. gambiae</option>
	<option value="repeat_7955"  >Zebrafish</option>
	<option value="repeat_7227"  >Fruit fly</option>
</select><br>
<INPUT TYPE="checkbox" NAME="MASK_LOOKUP_TABLE" VALUE="m">Mask for lookup table only<br>
<INPUT TYPE="checkbox" NAME="LCASE_MASK" VALUE="LCASE_MASK">Mask lower case letters

<?php echo $errorFilter; ?>

</TD>
</TR>
<TR>
<TD>
Database
</TD>
<TD>
<select name = "DATALIB">
<?php
$dbsArr = $sqlDataBase->query("SELECT * FROM dbs");
foreach($dbsArr as $row)
{
        extract($row);
        echo "<option value=".$id.">".$dbname."</option>";
}
?>
</select>
<?php echo $errorDataBase ?>
</TD>
</TR>
<TR>
<TD>
Expect:
</TD>
<TD>
<select name = "EXPECT">
    <option value=".0001"> 0.0001 </option>
    <option value=".01"> 0.01 </option>
    <option value="1"> 1 </option>
    <option value="10" selected> 10</option> 
    <option value="100"> 100 </option>
    <option value="1000"> 1000 </option>
</select>
<?php echo $errorExpect; ?>
</TD>
</TR>
<TR>
<TD>
Word Size:
</TD>
<TD>
<SELECT NAME="WORD_SIZE">
	<option value="16">16</option>
	<option value="20">20</option>
	<option value="24">24</option>
	<option value="28" selected>28</option>
	<option value="32">32</option>
	<option value="48">48</option>
	<option value="64">64</option>
	<option value="128">128</option>
	<option value="256">256</option>
</SELECT>
<?php echo $errorWordSize ?>
</TD>
</TR>
<TR>
<TD>
Matrix
</TD>
<TD>
<select name = "MAT_PARAM">
    <option value = "PAM301"> PAM30 </option>
    <option value = "PAM70"> PAM70 </option> 
    <option value = "BLOSUM80"> BLOSUM80 </option>
    <option selected value = "BLOSUM62"> BLOSUM62 </option>
    <option value = "BLOSUM45"> BLOSUM45 </option>
</select>
<?php echo $errorMatParam; ?>
<INPUT TYPE="checkbox" NAME="UNGAPPED_ALIGNMENT" VALUE="is_set"> Perform ungapped alignment 
<?php echo $errorUngappedAlignement; ?>
</TD>
</TR>
<TR>
<TD>
Compositional Adjustments:
</TD>
<TD>
<SELECT NAME="COMP_ADJ">
	<option value="0">No Composition-based statistics</option>
	<option value="2" selected> Composition-based score adjustments as in Bioinformatics 21:902-911</option>
	<option value="1">Composition-based statistics as in NAR 29:2994-3005, 2001</option>
	<option value="3">Composition-based score adjustment as in Bioinformatics 21:902-911,2005 unconditionally</option>
</SELECT>
<?php echo $errorCompAdj; ?>
</TD>
</TR>
<TR>
<TD>
Query Genetic Codes (blastx only):
</TD>
<TD>
<select name = "GENETIC_CODE">
 <option value="1"> Standard (1)</option> 
 <option value="2"> Vertebrate Mitochondrial (2) </option>
 <option value="3"> Yeast Mitochondrial (3) </option>
 <option value="4"> Mold Mitochondrial; ... (4) </option>
 <option value="5"> Invertebrate Mitochondrial (5) </option>
 <option value="6"> Ciliate Nuclear; ... (6) </option>
 <option value="7"> Echinoderm Mitochondrial (9) </option>
 <option value="10"> Euplotid Nuclear (10) </option>
 <option value="11"> Bacterial (11) </option>
 <option value="12"> Alternative Yeast Nuclear (12) </option>
 <option value="13"> Ascidian Mitochondrial (13) </option>
 <option value="14"> Flatworm Mitochondrial (14) </option>
 <option value="15"> Blepharisma Macronuclear (15) </option>
</select>
<?php echo $errorGeneticCode; ?>
</TD>
</TR>
<TR>
<TD>
Database Genetic Codes (tblast[nx] only):
</TD>
<TD>
<select name = "DB_GENETIC_CODE">
 <option value="1"> Standard (1) </option>
 <option value="2"> Vertebrate Mitochondrial (2) </option>
 <option value="3"> Yeast Mitochondrial (3) </option>
 <option value="4"> Mold Mitochondrial; ... (4) </option>
 <option value="5"> Invertebrate Mitochondrial (5) </option>
 <option value="6"> Ciliate Nuclear; ... (6) </option>
 <option value="9"> Echinoderm Mitochondrial (9) </option>
 <option value="10"> Euplotid Nuclear (10) </option>
 <option value="11"> Bacterial (11) </option>
 <option value="12"> Alternative Yeast Nuclear (12) </option>
 <option value="13"> Ascidian Mitochondrial (13) </option>
 <option value="14"> Flatworm Mitochondrial (14) </option>
 <option value="15"> Blepharisma Macronuclear (15) </option>
</select>
<?php echo $errorDBGeneticCode; ?>
</TD>
</TR>
<TR>
<TD>
Frame shift penalty (blastx):
</TD>
<TD>
<select NAME = "FRAME_SHIFT_PENALTY"> 
 <option value="6"> 6 </option>
 <option value="7"> 7 </option>
 <option value="8"> 8 </option>
 <option value="9"> 9 </option>
 <option value="10"> 10 </option>
 <option value="11"> 11 </option>
 <option value="12"> 12 </option>
 <option value="13"> 13 </option>
 <option value="14"> 14 </option>
 <option value="15"> 15 </option>
 <option value="16"> 16 </option>
 <option value="17"> 17 </option>
 <option value="18"> 18 </option>
 <option value="19"> 19 </option>
 <option value="20"> 20 </option>
 <option value="25"> 25 </option>
 <option value="30"> 30 </option>
 <option value="50"> 50 </option>
 <option value="1000"> 1000 </option>
 <option selected VALUE = "0"> No OOF </option>
</select>
<?php echo $errorFormatShiftPenalty; ?>
</TD>
</TR>
<TR>
<TD>
Descriptions
</TD>
<TD>
<select name = "DESCRIPTIONS">
    <option value="0"> 0 </option>
    <option value="10"> 10 </option>
    <option value="50"> 50 </option>
    <option value="100" selected> 10</option>0 
    <option value="250"> 250 </option>
    <option value="500"> 500 </option>
</select>
<?php echo $errorDescriptions; ?>
</TD>
</TR>
<TR>
<TD>
Alignments
</TD>
<TD>
<select name = "ALIGNMENTS">
    <option value="0"> 0 </option>
    <option value="10"> 10 </option>
    <option value="50" selected> 50 </option>
    <option value="100"> 100 </option>
    <option value="250"> 250 </option>
    <option value="500"> 500 </option>
</select>
<?php echo $errorAlignments; ?>
</TD>
</TR>
<TR>
<TD>
Match / Mismatch Scores:
</TD>
<TD>
<select name="MATCH_SCORES"> 
        <option value="1,-2" selected="selected" >1,-2</option>
	<option value="1,-3"  >1,-3</option>                  
	<option value="1,-4"  >1,-4</option>                  
	<option value="2,-3"  >2,-3</option>                  
	<option value="4,-5"  >4,-5</option>                  
	<option value="1,-1"  >1,-1</option>    
</select>
<?php echo $errorMatchScores; ?>
</TD>
</TR>
<TR>
<TD>
Gap Costs:
</TD>
<TD>
<select name="GAP_COSTS">
	<option value = "0 0" selected>Linear</option>   
	<option value = "5 2">Existence:  5 Extension: 2</option>				
	<option value = "2 2">Existence:  2 Extension: 2</option>				
	<option value = "1 2">Existence:  1 Extension: 2</option>
	<option value = "0 2">Existence:  0 Extension: 2</option>
	<option value = "3 1">Existence:  3 Extension: 1</option>		
	<option value = "2 1">Existence:  2 Extension: 1</option>		
	<option value = "1 1">Existence: 1 Extension: 1</option>				                                
</select>
<?php echo $errorGapCosts; ?>
</TD>
</TR>
<TR>
<TD>
Query strands to search against database:
</TD>
<TD>
<SELECT name="QUERY_STRANDS">
	<option value="3" selected>Both</option>
	<option value="1">Top</option>
	<option value="2">Bottom</option>
</SELECT>
<?php echo $errorQueryStrands; ?>
</TD>
</TR>
<TR>
<TD>
Output Format:
</TD>
<TD>
<SELECT NAME="FORMAT">
	<option value="0" selected>Pairwise</option>
	<option value="1">Master-slave showing identities</option>
	<option value="2">Master-slave no identities</option>
	<option value="3">Flat master-slave, show identities</option>
	<option value="4">Flat master-slave, no identities</option>
	<option value="5">Master-slave, no identities and blunt ends</option>
	<option value="6">Flat master-slave, no identities and blunt ends</optoin>
	<option value="7">XML Blast output</option>
	<option value="8">Tabular</option>
	<optoin value="9">Tabular with comment lines</option>
</SELECT>
<?php echo $errorFormat; ?>
</TD>
</TR>
<TR>
<TD>
</TD>
<TD>
<INPUT TYPE="submit" VALUE="Submit" name="submitfasta"><INPUT TYPE="reset" VALUE="Reset">
</TD>
</TR>
</TABLE>
</FORM>

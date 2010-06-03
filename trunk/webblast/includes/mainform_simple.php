<?php
include "includes/dbFormDropDown.php";

?>
<br>
<FORM ACTION=/webblast/index.php METHOD = POST NAME="MainBlastFormSimple" id="MainBlastFormSimple" ENCTYPE= "multipart/form-data">
<table>
<tr>
<th colspan=4 align="left">Job Settings</th>
</tr>
<tr>
<td>
<b>Job Name:</b>
</td>
<td colspan=>
<input type="text" name="jobname">
</td>
<td>
</td>
</tr>
<tr>
<td>
<b>Queries Per Chunk:<b> 
</td>
<td colspan=>
<select name="chunkSize">
		<?php
		$queryDefaultChunkSize = "SELECT chunksize FROM config ORDER BY id DESC LIMIT 1";
		$chunkSize = $sqlDataBase->singleQuery($queryDefaultChunkSize);
		echo "<option value=\"".$chunkSize."\" SELECTED>".$chunkSize."</option>";
		?>
		<option value="500">500</option>
		<option value="1000">1000</option>
		<option value="2000">2000</option>
		<option value="5000">5000</option>
		</select>
</td>
<td>
If your fasta file contains very fast running queries please select a larger chunk size, this will improve performance.
</td>
</tr>
<tr>
<th colspan=3 align="left"> BLAST Settings</th>
</tr>
<tr>
<td>
<div id="hiddenArea" >
<input type="checkbox" name="params[]" value="p" CHECKED>
</div>
<b>Program:</b>
</td>
<td>
<select name="inputp" size="0" onChange="fill_capital(MainBlastFormSimple.inputp.selectedIndex);">
<option value="0"></option>
<?php
$blastsArr=$sqlDataBase->query("SELECT * FROM blasts");
foreach($blastsArr as $row)
{
        extract($row);
        echo "<option value=".$id.">".$name."</option>";
}
?>
</select>
</td>
<td>
Program to use (-p)
</td>
</tr>
<tr>
<td>
<div id="hiddenArea" >
<input type="checkbox" name="params[]" value="d" CHECKED>
</div>
<b>Database:</b>
</td>
<td>
<select name="inputd" size="0">
<?php
/*
$dbsArr = $sqlDataBase->query("SELECT * FROM dbs");
foreach($dbsArr as $row)
{
        extract($row);
        echo "<option value=".$id.">".$dbname."</option>";
}
*/
?>
</select>
</td>
<td>
Database to use (-d)
</td>
</tr>
<tr>
<td>
<div id="hiddenArea" >
<input type="checkbox" name="params[]" value="i" CHECKED>
</div>
<b>Search Query:</b>
</td>
<td>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="10000000000">
<INPUT TYPE="file" NAME="inputi">
</td>
<td>
Fasta formatted query file to use (-i)
</td>
</tr>
<tr>
<td>
<div id="hiddenArea" >
<input type="checkbox" name="params[]" value="e" CHECKED>
</div>
<b>Expectation Value:</b>
</td>
<td>
<input type="text" name="inpute" value=10>
</td>
<td>
Expectation value (-e).
<div id="hiddenArea" >
        <input type="checkbox" name="params[]" value="o" CHECKED>
</div>
</td>
</tr>

<tr>
<th colspan=4 align="left">
BLAST Job Submission
</th>
<tr>
<td>
</td>
<td colspan=3>
<INPUT TYPE="submit" VALUE="Submit Blast Search" name="submitfasta">

<INPUT TYPE="reset" VALUE="Reset Inputs">
</td>
</tr>
</table>
<br><br>
<table>
<tr>
<th colspan=4 align="left">
Advanced BLAST Settings<br>
<font size=2>(Use the checkboxes to add more options to the search otherwise blastall defaults will be used)</font>
</th>
</tr>
<tr>
<th>
<b>Enable</b>
</th>
<th>
<b>Par.</b>
</th>
<th>
<b>Parameter Input</b>
</th>
<th>
<b>Parameter Info.</b>
</th>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="m"><td>m</td>
</td>
<td>
<select name="inputm" size="0">
<option value="0" selected>Pairwise</option>
        <option value="1">Master-slave showing identities</option>
        <option value="2">Master-slave no identities</option>
        <option value="3">Flat master-slave, show identities</option>
        <option value="4">Flat master-slave, no identities</option>
        <option value="5">Master-slave, no identities and blunt ends</option>
        <option value="6">Flat master-slave, no identities and blunt ends</optoin>
        <option value="7">XML Blast output</option>
        <option value="8">Tabular</option>
        <option value="9">Tabular with comment lines</option>

</select>
</td>
<td>
Alignment view options. If you choose <b>Pairwise</b> you will also be able to download comma separated values of the parsed results [<a href="csvsample.csv">Sample CSV</a>](-m) 
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="FU"><td>F</td>
</td>
<td>
<input type="text" name="inputFU" value="T">
</td>
<td>
Filter query (-F).
</td>
</tr>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="GU"><td>G</td>
</td>
<td>
<input type="text" name="inputGU" value="-1">
</td>
<td>
Cost to open a gap (zero invokes default behavior) [Integer] default = 0 (-G).
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="EU"><td>E</td>
</td>
<td>
<input type="text" name="inputEU" value="-1">
</td>
<td>
Cost to extend a gap (zero invokes default behavior) [Integer] default = 0 (-E).
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="XU"><td>X</td>
</td>
<td>
<input type="text" name="inputXU" value="0">
</td>
<td>
X dropoff value for gapped alignment (in bits) (zero invokes default behavior) blastn 30, megablast 20, tblastx 0, all others 15 [Integer] default = 0 (-X).
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="IU"><td>I</td>
</td>
<td>
<input type="checkbox" name="inputIU" value="T">
</td>
<td>
Show GI's in deflines [T/F] default = F (-I)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="q"><td>q</td>
</td>
<td>
<input type="text" name="inputq" value="-3">
</td>
<td>
Penalty for a nucleotide mismatch (blastn only) [Integer] default = -3 (-q)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="r"><td>r</td>
</td>
<td>
<input type="text" name="inputr" value="1">
</td>
<td>
Reward for a nucleotide match (blastn only) [Integer] default = 1 (-r)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="v" checked><td>v</td>
</td>
<td>
<input type="text" name="inputv" value="3">
</td>
<td>
Number of database sequences to show one-line descriptions for (V) [Integer] default = 10 (-v)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="b" checked><td>b</td>
</td>
<td>
<input type="text" name="inputb" value="3">
</td>
<td>
Number of database sequence to show alignments for (B) [Integer] default = 5 (-b)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="f"><td>f</td>
</td>
<td>
<input type="text" name="inputf" value="0">
</td>
<td>
Threshold for extending hits, default if zero blastp 11, blastn 0, blastx 12, tblastn 13 tblastx 13, megablast 0 [Integer] default = 0  (-f)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="g"><td>g</td>
</td>
<td>
<input type="checkbox" name="inputg" value="T" checked>
</td>
<td>
Perfom gapped alignment (not available with tblastx) [T/F] default = T (-g)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="QU"><td>Q</td>
</td>
<td>
<input type="text" name="inputQU" value="1">
</td>
<td>
Query Genetic code to use [Integer] default = 1 (-Q)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="DU"><td>D</td>
</td>
<td>
<input type="text" name="inputDU" value="1">
</td>
<td>
DB Genetic code (for tblast[nx] only) [Integer] default = 1 D)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="a" READONLY CHECKED><td>a</td>
</td>
<td>
<input type="text" name="inputa" value="2" READONLY">
</td>
<td>
Number of processors to use [Integer] default = 1 (-a)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="JU"><td>J</td>
</td>
<td>
<input type="checkbox" name="inputJU" value="T">
</td>
<td>
Believe the query defline [T/F] default = F (-J)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="MU"><td>M</td>
</td>
<td>
<SELECT name="inputMU">
<option value="BLOSUM62">BLOSUM62</option>
<option value="BLOSUM45">BLOSUM45</option>
<option value="BLOSUM80">BLOSUM80</option>
<option value="PAM30">PAM30</option>
<option value="PAM70">PAM70</option>
<option value="BLOSUM">BLOSUM</option>
</SELECT>
</td>
<td>
Matrix [String] default = BLOSUM62 (-M)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="WU"><td>W</td>
</td>
<td>
<input type="text" name="inputWU" value="0">
</td>
<td>
Word size, default if zero (blastn 11, megablast 28, all others 3) [Integer] default = 0 (-W)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="z"><td>z</td>
</td>
<td>
<input type="text" name="inputz" value="0">
</td>
<td>
Effective length of the database (use zero for the real size) [Real] default = 0 (-z)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="KU"><td>K</td>
</td>
<td>
<input type="text" name="inputKU" value="0">
</td>
<td>
Number of best hits from a region to keep. Off by default.<br>
If used a value of 100 is recommended.  Very high values of -v or -b is also suggested [Integer]<br>
default = 0
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="YU"><td>Y</td>
</td>
<td>
<input type="text" name="inputYU" value="0">
</td>
<td>
Effective length of the search space (use zero for the real size) [Real] default = 0 (-Y)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="SU"><td>S</td>
</td>
<td>
<input type="text" name="inputSU" value="3">
</td>
<td>
Query strands to search against database (for blast[nx], and tblastx) 3 is both, 1 is top, 2 is bottom [Integer] default = 3 (-S)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="TU"><td>T</td>
</td>
<td>
<input type="checkbox" name="inputTU" value="T">
</td>
<td>
Produce HTML output [T/F] default = F (-T)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="l"><td>l</td>
</td>
<td>
<input type="text" name="inputl" value=""> 
</td>
<td>
Restrict search of database to list of GI's [String] Optional 
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="UU"><td>U</td>
</td>
<td>
<input type="checkbox" name="inputUU" value="T">
</td>
<td>
Use lower case filtering of FASTA sequence [T/F] Optional default = F (-U)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="y"><td>y</td>
</td>
<td>
<input type="text" name="inputy" value="0.0">
</td>
<td>
X dropoff value for ungapped extensions in bits (0.0 invokes default behavior) blastn 20, megablast 10, all others 7 [Real] default = 0.0 (-y)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="ZU"><td>Z</td>
</td>
<td>
<input type="text" name="inputZU" value="0">
</td>
<td>
X dropoff value for final gapped alignment in bits (0.0 invokes default behavior) blastn/megablast 50, tblastx 0, all others 25 [Integer] default = 0 (-Z)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="RU" READONLY><td>R</td>
</td>
<td>
<input type="text" name="inputRU" READONLY>
</td>
<td>
PSI-TBLASTN checkpoint file [File In] Optional  (-R)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="n"><td>n</td>
</td>
<td>
<input type="checkbox" name="inputn" value="T">
</td>
<td>
MegaBlast search [T/F] default = F (-n)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="LU"><td>L</td>
</td>
<td>
<input type="text" name="inputLU" value="">
</td>
<td>
Location on query sequence [String] Optional (-L)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="AU"><td>A</td>
</td>
<td>
<input type="text" name="inputAU" value="0">
</td>
<td>
Multiple Hits window size, default if zero (blastn/megablast 0, all others 40 [Integer] default = 0 (-A)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="w"><td>w</td>
</td>
<td>
<input type="text" name="inputw" value="0">
</td>
<td>
Frame shift penalty (OOF algorithm for blastx) [Integer] default = 0 (-w)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="t"><td>t</td>
</td>
<td>
<input type="text" name="inputt" value="0">
</td>
<td>
Length of the largest intron allowed in tblastn for linking HSPs (0 disables linking) [Integer] default = 0 (-t)
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="BU"><td>B</td>
</td>
<td>
<input type="text" name="inputBU" value="0">
</td>
<td>
Number of concatenated queries, for blastn and tblastn [Integer]  Optional default = 0
</td>
</tr>
<tr>
<td>
<input type="checkbox" name="params[]" value="CU"><td>C</td>
</td>
<td>
<input type="text" name="inputCU" value="D">
</td>
<td>
-C  Use composition-based score adjustments for blastp or tblastn:<br>
      As first character:<br>
      D or d: default (equivalent to T)<br>
      0 or F or f: no composition-based statistics<br>
      2 or T or t: Composition-based score adjustments as in Bioinformatics 21:902-911,<br>
      1: Composition-based statistics as in NAR 29:2994-3005, 2001<br>
          2005, conditioned on sequence properties<br>
      3: Composition-based score adjustment as in Bioinformatics 21:902-911,<br>
          2005, unconditionally<br>
      For programs other than tblastn, must either be absent or be D, F or 0.<br>
           As second character, if first character is equivalent to 1, 2, or 3:<br>
      U or u: unified p-value combining alignment p-value and compositional p-value in round 1 only<br>
</tr>
</table>
</FORM>

<?php
include "includes/dbFormDropDown.php";
include "includes/profilesUpdateFields.php";

?>
<br>
<FORM ACTION="index.php" METHOD="POST" NAME="MainBlastFormSimple"
	id="MainBlastFormSimple" ENCTYPE="multipart/form-data">
	<table>
		<tr>
			<th colspan=4 align="left">Settings Profiles</th>
		</tr>
		<tr>
			<td><b>Profile:</b>
			</td>
			<td><SELECT name="profile" OnChange="OnSelectProfile(this)">
					<option value="0">Default</option>
					<?php
					$queryProfiles = "SELECT id,name FROM blast_profiles WHERE userid=".$userid;
					$profiles = $sqlDataBase->query($queryProfiles);

					foreach($profiles as $id=>$profile)
					{
						echo "<option value=".$profile['id'].">".$profile['name']."</option>";
					}

					?>
			</SELECT>
			</td>
			<td>Saved blast query parameters profiles.</td>
		</tr>
		<tr>
			<td><b>New Profile Name:</b>
			</td>
			<td><input type="text" name="saveProfileName"><input type="submit"
				name="saveProfile" value="Save">
			</td>
			<td>Save current inputed settings to profile for future use.</td>
		</tr>
		<tr>
			<th colspan=4 align="left">Job Settings</th>
		</tr>
		<tr>
			<td><b>Job Name:</b>
			</td>
			<td colspan=><input type="text" name="jobname">
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Queries Per Chunk:</b>
			
			</td>
			<td colspan=><select name="chunkSize" id="chunkSize">
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
			<td>Please use 200 if you are using <b>BLASTX</b> on a large database. <br>If your fasta file contains very fast running queries please
				select a larger chunk size, this will improve performance.</td>
		</tr>
		<tr>
			<th colspan=3 align="left">BLAST Settings</th>
		</tr>
		<tr>
			<td>
				<div id="hiddenArea">
					<input type="checkbox" id="p_box" name="params[]" value="p" CHECKED>
				</div> <b>Program:</b>
			</td>
			<td><select name="inputp" id="p" size="0"
				onChange="fill_capital(MainBlastFormSimple.inputp.selectedIndex);">
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
			<td>Program to use (-p)</td>
		</tr>
		<tr>
			<td>
				<div id="hiddenArea">
					<input type="checkbox" name="params[]" value="d" id="d_box" CHECKED>
				</div> <b>Database:</b>
			</td>
			<td><select name="inputd" id="d" size="0">
					<option value=0 SELECTED></option>
			</select>
			</td>
			<td>Database to use (-d)</td>
		</tr>
		<tr>
			<td>
				<div id="hiddenArea">
					<input type="checkbox" name="params[]" id="i_box" value="i" CHECKED>
				</div> <b>Search Query File:</b>
			</td>
			<td><input type="hidden" name="MAX_FILE_SIZE" value="1610612736" /> <INPUT
				TYPE="file" NAME="inputi">
			</td>
			<td rowspan="2">Fasta formatted query file to use (-i)</td>
		</tr>
		<tr>
			<td><b>Search Query URL:</b>
			</td>
			<td><TEXTAREA name="inputiURL"></TEXTAREA>
			</td>
		</tr>
		<tr>
			<td>
				<div id="hiddenArea">
					<input type="checkbox" name="params[]" value="e" id="e_box" CHECKED>
				</div> <b>Expectation Value:</b>
			</td>
			<td><input type="text" name="inpute" id="e" value=10>
			</td>
			<td>Expectation value (-e).
				<div id="hiddenArea">
					<input type="checkbox" name="params[]" value="o" id="o_box" CHECKED>
				</div>
			</td>
		</tr>
		<tr>
			<th colspan=4 align="left">BLAST Job Submission</th>
		
		</tr>
		<tr>
			<td></td>
			<td colspan=3>
			<INPUT TYPE="submit" VALUE="Submit Blast Search" name="submitfasta"> 
			<INPUT TYPE="reset" VALUE="Reset Inputs">
			</td>
		</tr>
	</table>
	<br> <br>
	<table>
		<tr>
			<th colspan=4 align="left">Advanced BLAST Settings<br> <font size=2>(Use
					the checkboxes to add more options to the search otherwise blastall
					defaults will be used)</font>
			</th>
		</tr>
		<tr>
			<th><b>Enable</b>
			</th>
			<th><b>Par.</b>
			</th>
			<th><b>Parameter Input</b>
			</th>
			<th><b>Parameter Info.</b>
			</th>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" value="m" id="m_box">
			</td>
			<td>m</td>
			<td><select name="inputm" id="m" size="0">
					<option value="0" selected>Pairwise</option>
					<option value="1">Master-slave showing identities</option>
					<option value="2">Master-slave no identities</option>
					<option value="3">Flat master-slave, show identities</option>
					<option value="4">Flat master-slave, no identities</option>
					<!--<option value="5">Master-slave, no identities and blunt ends</option> -->
					<!--<option value="6">Flat master-slave, no identities and blunt ends</option>-->
					<option value="7">XML Blast output</option>
					<option value="8">Tabular</option>
					<option value="9">Tabular with comment lines</option>

			</select>
			</td>
			<td>Alignment view options. If you choose <b>Pairwise Blast output</b> you
				will also be able to download comma separated values of the parsed
				results [<a href="csvsample.csv">Sample CSV</a>](-m)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="FU_box" value="FU">
			</td>
			<td>F</td>
			<td><input type="text" name="inputFU" id="FU" value="T">
			</td>
			<td>Filter query (-F).</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="GU_box" value="GU">
			</td>
			<td>G</td>

			<td><input type="text" name="inputGU" id="GU" value="-1">
			</td>
			<td>Cost to open a gap (zero invokes default behavior) [Integer]
				default = 0 (-G).</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="EU_box" value="EU">
			</td>
			<td>E</td>
			
			<td><input type="text" name="inputEU" id="EU" value="-1">
			</td>
			<td>Cost to extend a gap (zero invokes default behavior) [Integer]
				default = 0 (-E).</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="XU_box" value="XU">
			</td>
			<td>X</td>
			
			<td><input type="text" name="inputXU" id="XU" value="0">
			</td>
			<td>X dropoff value for gapped alignment (in bits) (zero invokes
				default behavior) blastn 30, megablast 20, tblastx 0, all others 15
				[Integer] default = 0 (-X).</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="IU_box" value="IU">
			</td>
			<td>I</td>
			
			<td><input type="checkbox" name="inputIU" id="IU" value="T">
			</td>
			<td>Show GI's in deflines [T/F] default = F (-I)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="q_box" value="q">
			</td>
			<td>q</td>
			
			<td><input type="text" name="inputq" id="q" value="-3">
			</td>
			<td>Penalty for a nucleotide mismatch (blastn only) [Integer] default
				= -3 (-q)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="r_box" value="r">
			</td>
			<td>r</td>
			
			<td><input type="text" name="inputr" id="r" value="1">
			</td>
			<td>Reward for a nucleotide match (blastn only) [Integer] default = 1
				(-r)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="v_box" value="v"
				checked>
			</td>
			<td>v</td>
			
			<td><input type="text" name="inputv" id="v" value="3">
			</td>
			<td>Number of database sequences to show one-line descriptions for
				(V) [Integer] default = 10 (-v)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="b_box" value="b"
				checked>
			</td>
			<td>b</td>
			
			<td><input type="text" name="inputb" id="b" value="3">
			</td>
			<td>Number of database sequence to show alignments for (B) [Integer]
				default = 5 (-b)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="f_box" value="f">
			</td>
			<td>f</td>
			
			<td><input type="text" name="inputf" id="f" value="0">
			</td>
			<td>Threshold for extending hits, default if zero blastp 11, blastn
				0, blastx 12, tblastn 13 tblastx 13, megablast 0 [Integer] default =
				0 (-f)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="g_box" value="g">
			</td>
			<td>g</td>
			
			<td><input type="checkbox" name="inputg" id="g" value="T" checked>
			</td>
			<td>Perfom gapped alignment (not available with tblastx) [T/F]
				default = T (-g)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="QU_box" value="QU">
			</td>
			<td>Q</td>
			
			<td><input type="text" name="inputQU" id="QU" value="1">
			</td>
			<td>Query Genetic code to use [Integer] default = 1 (-Q)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="DU_box" value="DU">
			</td>
			<td>D</td>
			
			<td><input type="text" name="inputDU" id="DU" value="1">
			</td>
			<td>DB Genetic code (for tblast[nx] only) [Integer] default = 1 D)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="a_box" value="a"
				READONLY CHECKED>
			</td>
			<td>a</td>
			
			<td><input type="text" name="inputa" value="2" id="a" READONLY>
			</td>
			<td>Number of processors to use [Integer] default = 1 (-a)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="JU_box" value="JU">
			</td>
			<td>J</td>
			
			<td><input type="checkbox" name="inputJU" id="JU" value="T">
			</td>
			<td>Believe the query defline [T/F] default = F (-J)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="MU_box" value="MU">
			</td>
			<td>M</td>
			
			<td><SELECT name="inputMU" id="MU">
					<option value="BLOSUM62">BLOSUM62</option>
					<option value="BLOSUM45">BLOSUM45</option>
					<option value="BLOSUM80">BLOSUM80</option>
					<option value="PAM30">PAM30</option>
					<option value="PAM70">PAM70</option>
					<option value="BLOSUM">BLOSUM</option>
			</SELECT>
			</td>
			<td>Matrix [String] default = BLOSUM62 (-M)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="WU_box" value="WU">
			</td>
			<td>W</td>
			
			<td><input type="text" name="inputWU" id="WU" value="0">
			</td>
			<td>Word size, default if zero (blastn 11, megablast 28, all others
				3) [Integer] default = 0 (-W)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="z_box" value="z">
			</td>
			<td>z</td>
			
			<td><input type="text" name="inputz" id="z" value="0">
			</td>
			<td>Effective length of the database (use zero for the real size)
				[Real] default = 0 (-z)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="KU_box" value="KU">
			</td>
			<td>K</td>
			
			<td><input type="text" name="inputKU" id="KU" value="0">
			</td>
			<td>Number of best hits from a region to keep. Off by default.<br> If
				used a value of 100 is recommended. Very high values of -v or -b is
				also suggested [Integer]<br> default = 0</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="YU_box" value="YU">
			</td>
			<td>Y</td>
			
			<td><input type="text" name="inputYU" id="YU" value="0">
			</td>
			<td>Effective length of the search space (use zero for the real size)
				[Real] default = 0 (-Y)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="SU_box" value="SU">
			</td>
			<td>S</td>
			
			<td><input type="text" name="inputSU" id="SU" value="3">
			</td>
			<td>Query strands to search against database (for blast[nx], and
				tblastx) 3 is both, 1 is top, 2 is bottom [Integer] default = 3 (-S)
			</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="TU_box" value="TU">
			</td>
			<td>T</td>
			
			<td><input type="checkbox" name="inputTU" id="TU" value="T">
			</td>
			<td>Produce HTML output [T/F] default = F (-T)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="l_box" value="l">
			</td>
			<td>l</td>
			
			<td><input type="text" name="inputl" id="l" value="">
			</td>
			<td>Restrict search of database to list of GI's [String] Optional</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="UU_box" value="UU">
			</td>
			<td>U</td>
			
			<td><input type="checkbox" name="inputUU" id="UU" value="T">
			</td>
			<td>Use lower case filtering of FASTA sequence [T/F] Optional default
				= F (-U)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="y_box" value="y">
			</td>
			<td>y</td>
			
			<td><input type="text" name="inputy" id="y" value="0.0">
			</td>
			<td>X dropoff value for ungapped extensions in bits (0.0 invokes
				default behavior) blastn 20, megablast 10, all others 7 [Real]
				default = 0.0 (-y)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="ZU_box" value="ZU">
			</td>
			<td>Z</td>
			
			<td><input type="text" name="inputZU" id="ZU" value="0">
			</td>
			<td>X dropoff value for final gapped alignment in bits (0.0 invokes
				default behavior) blastn/megablast 50, tblastx 0, all others 25
				[Integer] default = 0 (-Z)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="RU_box" value="RU"
				READONLY>
			</td>
			<td>R</td>
			
			<td><input type="text" name="inputRU" id="RU" READONLY>
			</td>
			<td>PSI-TBLASTN checkpoint file [File In] Optional (-R)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="n_box" value="n">
			</td>
			<td>n</td>
			
			<td><input type="checkbox" name="inputn" id="n" value="T">
			</td>
			<td>MegaBlast search [T/F] default = F (-n)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="LU_box" value="LU">
			</td>
			<td>L</td>
			
			<td><input type="text" name="inputLU" id="LU" value="">
			</td>
			<td>Location on query sequence [String] Optional (-L)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="AU_box" value="AU">
			</td>
			<td>A</td>
			
			<td><input type="text" name="inputAU" id="AU" value="0">
			</td>
			<td>Multiple Hits window size, default if zero (blastn/megablast 0,
				all others 40 [Integer] default = 0 (-A)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="w_box" value="w">
			</td>
			<td>w</td>
			
			<td><input type="text" name="inputw" id="w" value="0">
			</td>
			<td>Frame shift penalty (OOF algorithm for blastx) [Integer] default
				= 0 (-w)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="t_box" value="t">
			</td>
			<td>t</td>
			
			<td><input type="text" name="inputt" id="t" value="0">
			</td>
			<td>Length of the largest intron allowed in tblastn for linking HSPs
				(0 disables linking) [Integer] default = 0 (-t)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="BU_box" value="BU"
				readonly>
			</td>
			<td>B</td>
			
			<td><input type="text" name="inputBU" id="BU" value="0">
			</td>
			<td>Number of concatenated queries, for blastn and tblastn [Integer]
				Optional default = 0</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="params[]" id="CU_box" value="CU">
			</td>
			<td>C</td>
			
			<td><input type="text" name="inputCU" id="CU" value="D">
			</td>
			<td>-C Use composition-based score adjustments for blastp or tblastn:<br>
				As first character:<br> D or d: default (equivalent to T)<br> 0 or F
				or f: no composition-based statistics<br> 2 or T or t:
				Composition-based score adjustments as in Bioinformatics 21:902-911,<br>
				1: Composition-based statistics as in NAR 29:2994-3005, 2001<br>
				2005, conditioned on sequence properties<br> 3: Composition-based
				score adjustment as in Bioinformatics 21:902-911,<br> 2005,
				unconditionally<br> For programs other than tblastn, must either be
				absent or be D, F or 0.<br> As second character, if first character
				is equivalent to 1, 2, or 3:<br> U or u: unified p-value combining
				alignment p-value and compositional p-value in round 1 only<br>
		
		</tr>
	</table>
</FORM>

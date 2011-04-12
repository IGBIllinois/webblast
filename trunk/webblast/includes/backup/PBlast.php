<?php

?>
           <div id="content">            
        <!-- Do errors this way -->        
        <!--<ul class="msg "><li class=""><p class=""></p></li></ul>-->        
        <ul class="msg "><li class=""></li></ul>
        <div class="bc-tabs">
        <ul>               
        <li><a class="progLinks" id="blastnTab" href="Blast75e9.html?PROGRAM=blastn&amp;BLAST_PROGRAMS=megaBlast&amp;PAGE_TYPE=BlastSearch&amp;SHOW_DEFAULTS=on&amp;BLAST_SPEC=&amp;LINK_LOC=blasttab" title="Search a nucleotide databases using a nucleotide query">blastn</a></li>
<li class="tab hidden">&nbsp</li>
<li class="tab-lh">&nbsp</li>
<li class="tab-current" ><a class="progLinks" id="blastpTab" href="Blast2777.html?PROGRAM=blastp&amp;BLAST_PROGRAMS=blastp&amp;PAGE_TYPE=BlastSearch&amp;SHOW_DEFAULTS=on&amp;BLAST_SPEC=&amp;LINK_LOC=blasttab" title="Search a protein databases using a protein query">blastp</a></li>
<li class="tab-rh">&nbsp</li>
<li><a  class="progLinks" id="blastxTab" href="Blast0e49.html?PROGRAM=blastx&amp;BLAST_PROGRAMS=blastx&amp;PAGE_TYPE=BlastSearch&amp;SHOW_DEFAULTS=on&amp;BLAST_SPEC=&amp;LINK_LOC=blasttab" title="Search protein databases using a translated nucleotide query">blastx</a></li>                
<li class="tab ">&nbsp</li> 
<li><a class="progLinks" id="tblastnTab" href="Blastbdf6.html?PROGRAM=tblastn&amp;BLAST_PROGRAMS=tblastn&amp;PAGE_TYPE=BlastSearch&amp;SHOW_DEFAULTS=on&amp;BLAST_SPEC=&amp;LINK_LOC=blasttab" title="Search translated nucleotide database using a protein query">tblastn</a></li>                
<li class="tab ">&nbsp</li> 
<li><a class="progLinks" id="tblastxTab" href="Blastf0a4.html?PROGRAM=tblastx&amp;BLAST_PROGRAMS=tblastx&amp;PAGE_TYPE=BlastSearch&amp;SHOW_DEFAULTS=on&amp;BLAST_SPEC=&amp;LINK_LOC=blasttab" title="Search translated nucleotide database using a translated nucleotide query">tblastx</a></li> 
<li class="tab ">&nbsp</li> 
        <li class="orgTitle"></li>
        </ul>
        </div>        
        <!-- <form name="searchForm" action="Blast.cgi" enctype="application/x-www-form-urlencoded"  method="post" class="f-wrap-1" id="searchForm">-->        
        <form name="searchForm" action="http://blast.ncbi.nlm.nih.gov/Blast.cgi" enctype="multipart/form-data"  method="post" class="f-wrap-1 all" id="searchForm">
         <div id="progDescr">BLASTP programs search protein databases using a protein query.
         <a href="http://www.ncbi.nlm.nih.gov/blast/producttable.shtml#blastp" id="progHelp" title="help" target="helpWin" class="help ">more...</a>
         
         </div>
        <div id="pagelinks">
               <a href="#" class="resetlink" resetform="searchForm">Reset page</a>
               <a href="#" name="getURL" >Bookmark</a>               
        </div>
        <div id="query" class="section">                
   <fieldset>
      <legend>Enter Query Sequence</legend>       
      <div class="qs">
         <div class="formblock all" id="qseq">
            <label for="seq">Enter accession number, gi, or FASTA sequence                    
               <a class="helplink hiding" title="help" href="#" id="queryHelp"><span>[?]</span></a>
               <a href="#" class="clearlink" fieldToClear="seq" id="clearquery">Clear</a>                              
            </label>
            <div toggle="queryHelp" class="helpbox hidden" id="hq">
                Query sequence(s) to be used for a BLAST search should be pasted in the text area. 
                It automatically determines the format or the input. To allow this feature there 
                are certain conventions required with regard to the input of identifiers.
                <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml" target="helpWin" class="helplink">more...</a> 
                </div>                                                                              
            <textarea id="seq" class="reset" rows="5" cols="80" name="QUERY" ></textarea>
            <br />
            <!-- This of course would have to change with selected program -->
            <input type="hidden" id="db" value="protein" name="db"/>
         </div>   
      
         <div id="qrange" class="formdetail">
            <h4 class="withnote">Query subrange <a href="#" id="srHelp" class="helplink hiding"><span>[?]</span></a> </h4>
            <table>
            <tr>
            <td><label for="QUERY_FROM">From</label></td>
            <td class="all"><input type="text" size="12" class="reset" id="QUERY_FROM" name="QUERY_FROM" value="" /></td>
            </tr>
            <tr>
            <td><label for="QUERY_TO">To</label></td>
            <td class="all"><input type="text" size="12" class="reset" name="QUERY_TO" id="QUERY_TO" value="" /></td>
            </tr>
            </table>
         <p toggle="srHelp" class="helpbox hidden" id="helprange">
               Enter coordinates for a <strong>subrange</strong> of the
               query sequence. The BLAST search will apply only to the
               residues in the range.  Sequence coordinates are from 1
               to the sequence length.The range includes the residue at
               the <strong>To</strong> coordinate.
               <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#get_subsequence" target="helpWin" >more...</a>
         </p>
         &nbsp; <!-- Hack for Mozilla, this time... -->
      </div><!-- /#qrange -->
      </div><!-- /.qs -->

      <label for="upl" class="m" >Or, upload file</label>
      <div class="input  all">
      <input type="file" id="upl" name="QUERYFILE" />             
      <a class="helplink hiding" title="help" id="uploadHelp" href="#"><span>[?]</span></a>
      <p toggle="uploadHelp" class="helpbox hidden">
       Use the browse button to upload a file from your local disk.
       The file may contain a single sequence or a list of sequences.
       The data may be either a list of database accession numbers,
       NCBI gi numbers, or sequences in FASTA format.        
      </p>      
          </div>
      
          <div class="m  blastx tblastx" id="gencode">
      <label for="GENETIC_CODE" class="m">Genetic code</label>
      <div class="input blastx tblastx">
        <select name="GENETIC_CODE"  class="reset" id="GENETIC_CODE">
        <option value="1"  >Standard (1)</option>
         <option value="2"  >Vertebrate Mitochondrial (2)</option>
         <option value="3"  >Yeast Mitochondrial (3)</option>
         <option value="4"  >Mold Mitochondrial; ... (4)</option>
         <option value="5"  >Invertebrate Mitochondrial (5)</option>
         <option value="6"  >Ciliate Nuclear; ... (6)</option>
         <option value="9"  >Echinoderm Mitochondrial (9)</option>
         <option value="10"  >Euplotid Nuclear (10)</option>
         <option value="11"  >Bacteria and Archaea (11)</option>
         <option value="12"  >Alternative Yeast Nuclear (12)</option>         
         <option value="13"  >Ascidian Mitochondrial (13)</option>
         <option value="14"  >Flatworm Mitochondrial (14)</option>
         <option value="15"  >Blepharisma Macronuclear (15)</option>
        </select>
      </div>      
      </div>            
      <!-- <div class="input all"> -->
   <label for="qtitle" class="m" >Job Title</label>
   <div class="input  all">
      <input name="JOB_TITLE" id="qtitle" class="reset" size="80" value="" /><br />
      <span class="help">Enter a descriptive title for your BLAST search</span>      
      <a class="helplink hiding" title="help" href="#" id="titleHelp"><span>[?]</span></a>
      <p toggle="titleHelp" class="helpbox hidden">
                This title appears on all BLAST results and saved searches.             
      </p>      
   </div> 
   <div id="bl2" class="" >
      <input class="" type="checkbox" name="BL2SEQ" id="bl2seq"   />
      <label class="inlineLabel all" for="bl2seq">Align two or more sequences</label>       
       <a class="helplink hiding" title="help" href="#" id="bl2seqHelp"><span>[?]</span></a>
       <p toggle="bl2seqHelp" class="helpbox hidden">
       Align two or more sequences using BLAST. To use BLAST to align sequences one or more queries should be entered
       in the top text box and one or more subject sequences should be entered in the lower text box.
       Then use the BLAST button at the bottom of the page to align your sequences
       <br />
       <span class="cdsHelp blastn">
       To get the CDS annotation in the output, use only the NCBI accession or
       gi number for either the query or subject. Reformat the results and check 'CDS feature' to display that annotation.
       </span>
       </p>       
   </div>            
   </fieldset>   
</div>
<div id="subject" class="section blast2seq">
    
   <fieldset>
      <legend>Enter Subject Sequence</legend>
   
      <div class="qs">
         <div class="formblock all" id="sseq">
            <label for="seq">Enter accession number, gi, or FASTA sequence
               <a class="helplink hiding" title="help" href="#" id="subjectHelp"><span>[?]</span></a>
               <a href="#" class="clearlink" fieldToClear="subj" id="subjectClear">Clear</a>                              
            </label>
            <div toggle="subjectHelp" class="helpbox hidden" id="hs">
                Subject sequence(s) to be used for a BLAST search should be pasted in the text area. 
                It automatically determines the format or the input. To allow this feature there 
                are certain conventions required with regard to the input of identifiers.
                <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml" target="helpWin" class="helplink">more...</a> 
                </div>                                                                              
            <textarea id="subj" class="reset" rows="5" cols="80" name="SUBJECTS" ></textarea>
            <!-- This of course would have to change with selected program -->
            <input type="hidden" id="stype" value="protein" name="stype"/>
            <br />            
         </div>   
      
         <div id="srange" class="formdetail">
            <h4 class="withnote">Subject subrange <a href="#" id="s_srHelp" class="helplink hiding"><span>[?]</span></a> </h4>
            <table>
            <tr>
            <td><label for="SUBJECTS_FROM">From</label></td>
            <td class="all"><input type="text" size="12" class="reset" id="SUBJECTS_FROM" name="SUBJECTS_FROM" value="" /></td>
            </tr>
            <tr>
            <td><label for="SUBJECTS_TO">To</label></td>
            <td class="all"><input type="text" size="12" class="reset" name="SUBJECTS_TO" id="SUBJECTS_TO" value="" /></td>
            </tr>
            </table>
         <p toggle="s_srHelp" class="helpbox hidden" id="s_helprange">
               Enter coordinates for a <strong>subrange</strong> of the
               subject sequence. The BLAST search will apply only to the
               residues in the range.  Sequence coordinates are from 1
               to the sequence length.The range includes the residue at
               the <strong>To</strong> coordinate.
               <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#get_subsequence" target="helpWin" >more...</a>
         </p>
         &nbsp; <!-- Hack for Mozilla, this time... -->
         </div><!-- /#srange -->
      </div><!-- /.qs -->

      <label for="s_upl" class="m" >Or, upload file</label>
      <div class="input  all">
      <input type="file" id="s_upl" name="SUBJECTFILE" />             
      <a class="helplink hiding" title="help" id="s_uploadHelp" href="#"><span>[?]</span></a>
      <p toggle="s_uploadHelp" class="helpbox hidden">
       Use the browse button to upload a file from your local disk.
       The file may contain a single sequence or a list of sequences.
       The data may be either a list of database accession numbers,
       NCBI gi numbers, or sequences in FASTA format.        
      </p>      
          </div>
   
   </fieldset>   
</div>


<!-- ??? 
1.Add correct help links after the page is done 
2. For translations: check this in .js file TranslationCombinations
3. Add the same class handling for hidden fields 
-->
  
  <div id="choosedb" class="section">
  
  <fieldset>

    <legend>Choose Search Set</legend>      
    
    <div id="dblist">
    <label for="DATABASE" class="m">Database</label>    
    <div class="input all">    
      
      
      <div class="all dbdata">      
      <select name="DATABASE" id="DATABASE" class="reset checkDef" defVal="nr">
<!-- blastp,psiblast,phibalst or blastx -->
<option value="nr"  >Non-redundant protein sequences (nr)</option>
<option value="refseq_protein"  >Reference proteins (refseq_protein)</option>
<option value="swissprot"  >Swissprot protein sequences(swissprot)</option>
<option value="pat"  >Patented protein sequences(pat)</option>
<option value="pdb"  >Protein Data Bank proteins(pdb)</option>
<option value="env_nr"  >Environmental samples(env_nr)</option>	 
</select> 
<a class="togsrc helplink hiding" title="help" id="dbHelp" href="#"><span>[?]</span></a>
<p toggle="dbHelp" class="togdst helpbox hidden">
Select the sequence database to run searches against. No
BLAST database contains all the sequences at NCBI. BLAST
databases are organized by informational content (nr, RefSeq, etc.)
or by sequencing technique (WGS, EST, etc.).
<a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#protein_databases" target="helpWin" >more...</a>      
      </div>      
    </div>
    </div>
    
   <div id="orgInfo" class="">
   <label for="qorganism" class="m">Organism<br />
      <span class="hint">Optional</span>
   </label>
    
   <div class="input">       
    <div id="chooseOther">
        <input name="EQ_MENU" size="55" id="qorganism" value="" sgDict="taxids_sg" sgDelay="50" sgRunFunc="" sgStyle="SG_green" />
        <input type="checkbox" name="ORG_EXCLUDE"  class="reset oExclR" id="orgExcl"/>        
        <input type="hidden" value = "1" name="NUM_ORG" id="numOrg" />
        <label for="orgExcl" class="right">Exclude</label>
        <img border="0" src="css/images/addOrg.jpg" id="addOrg"   alt="Add organism"  mouseovImg="css/images/addOrgOver.jpg" mouseoutImg="css/images/addOrg.jpg" mousedownImg="css/images/addOrgDown.jpg" mouseupImg="css/images/addOrgOver.jpg"  />
        
        <div id="orgs">
        
        </div>
        <p class="help">Enter organism common name, binomial, or tax id. Only 20 top taxa will be shown.
        <a id="selorg" class="helplink hiding" title="help" href="#"><span>[?]</span></a>
        </p>        
        
     </div>     
     <p class="helpbox hidden" toggle="selorg">
            Select from the list or choose “Custom” to enter the name of an organism. 
            The search will be restricted to the sequences in the database which are from the organism selected.
      </p>

   </div>
   </div>
   
   <div id="excl">
   <label for="qquery" class="m">Exclude<br />
     <span class="hint">Optional</span>
   </label>
    <div class="input all">
    <input type="checkbox" name="EXCLUDE_MODELS" class="reset" id="exclModels"   />
    <label class="right inlinelabel" for="exclModels">Models (XM/XP)</label>
    <span id="exclSeq" class="blastp">
    <input type="checkbox" name="EXCLUDE_SEQ_UNCULT" class="reset" id="exclSeqUncult"   />
    <label class="right inlinelabel" for="exclSeqUncult" id="exclSeqUncultLb">Sequences from uncultured bacteria</label>
    </span>
    </div>
    </div>
    
   <div id="entrezQuery">
   <label for="qquery" class="m">Entrez Query<br />
     <span class="hint">Optional</span>
   </label>

   <div class="input all">
      <input name="EQ_TEXT" id="qquery" class="reset" size="50" value="" /><br/>
      <span class="help">Enter an Entrez query to limit search</span>      
      <a class="helplink hiding" title="help" id="entrezHelp" href="#"><span>[?]</span></a>
      <p toggle="entrezHelp" class="helpbox hidden">
                        You can use Entrez query syntax to search a subset of the selected BLAST database. 
                        This can be helpful to limit searches to molecule types, sequence lengths or to exclude organisms.            
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#entrez_query" target="helpWin" >more...</a>
      </p>

    </div>
   </div>     
  </fieldset> 

</div><!--/#choosedb-->

<div class="all blastp" id="optSection">
<div id="sopts" class="section">
<!-- Note: tables in the page used only to get around an IE presentation bug.
           Problem: the first <input> tag after a floated element causes
                    a line break that it shouldn't cause. Happens only in IE.
           If you can fix this bug, do so, and remove the tables from this page.
-->

  <fieldset id="progSel" class="blastp psiBlast phiBlast blastn megaBlast discoMegablast">

    <legend>Program Selection</legend>   

    <label class="m blastn megaBlast discoMegablast">Optimize for</label>
            
    <div class="input blastn megaBlast discoMegablast">        
    <table>
    <tr><td>      
      <!-- onclick= SetProgTypeDefaults() - adjust lists on the page corresponding to the program -->
      <input name="BLAST_PROGRAMS" id="megaBlast" type="radio" value="megaBlast"  />
      <label for="megaBlast" class="right">Highly similar sequences (megablast)</label>
    </td></tr>    
             
    <tr><td>      
      <input name="BLAST_PROGRAMS" id="discoMegablast" type="radio" value="discoMegablast"  />
      <label for="discoMegablast" class="right">More dissimilar sequences (discontiguous megablast)</label>
    </td></tr>
    
    <tr><td>      
      <input name="BLAST_PROGRAMS" id="blastn" type="radio" value="blastn"  />
      <label for="blastn" class="right">Somewhat similar sequences (blastn)</label>
    </td></tr>
     
     <tr><td>
      <span class="help">Choose a BLAST algorithm</span>
      <a class="helplink hiding" title="help" href="#" id="algNucHelp"><span>[?]</span></a>
      <div toggle="algNucHelp" class="helpbox hidden">
        <ul>
                <li>Megablast is intended for comparing a query to closely related sequences and works best 
                if the target percent identity is 95% or more but is very fast.</li>
                <li>Discontiguous megablast uses an initial seed that ignores some bases (allowing mismatches) 
                and is intended for cross-species comparisons.</li>  
                <li>BlastN is slow, but allows a word-size down to seven bases.</li>            
                </ul>
                <a href="http://www.ncbi.nlm.nih.gov/blast/producttable.shtml#tab31" target="helpWin" >more...</a>
                
      </div>
     </td></tr>
      </table>
   </div>
      
   <!-- <label class="m blastp phi psi">Algorithm</label> -->
   <label class="m blastp psiBlast phiBlast">Algorithm</label>

   <!-- <div class="input blastp phi psi"> -->
   <div class="input blastp psiBlast phiBlast">

   <table>
      <tr><td>      
      <!-- onclick= SetProgTypeDefaults() - adjust lists on the page corresponding to the program -->
      <input name="BLAST_PROGRAMS" id="blastp" type="radio" value="blastp" checked="checked" />
      <label for="blastp" class="right">blastp (protein-protein BLAST)</label>
      </td></tr>
     
      <tr id="psiAll"><td>      
      <input name="BLAST_PROGRAMS" id="psi" type="radio" value="psiBlast"  />
      <label for="psi" class="right">PSI-BLAST (Position-Specific Iterated BLAST)</label>
      </td></tr>
       
      <tr id="phiAll"><td>      
      <input name="BLAST_PROGRAMS" id="phi" type="radio" value="phiBlast"  />
      <label for="phi" class="right">PHI-BLAST (Pattern Hit Initiated BLAST)</label>
     
      <div class="moreInput phiBlast" showIf="phi">
         <input class="phiBlast reset" name="PHI_PATTERN" id="PHI_PATTERN" size="40" type="text" value="" /><br />
         <span class="help">Enter a PHI pattern</span>         
         <a class="helplink hiding" title="help" href="#" id="phiHelp"><span>[?]</span></a>
         <p toggle="phiHelp" class="helpbox hidden">

        Enter a PHI pattern to start the search.  PHI-BLAST may
        perform better than simple pattern searching because it
        filters out false positives (pattern matches that are probably
        random and not indicative of homology).

             </p>
      </div>
      </td></tr>
      
      <tr><td>      
      <span class="help">Choose a BLAST algorithm</span>
      <a class="helplink hiding" title="help" href="#" id="algProtHelp"><span>[?]</span></a>
      <div toggle="algProtHelp" class="helpbox hidden">
        <ul>
                <li>BlastP simply compares a protein query to a protein database.</li>
                <li>PSI-BLAST allows the user to build a PSSM (position-specific scoring matrix) using the results of the first BlastP run.)</li>  
                <li>PHI-BLAST performs the search but limits alignments to those that match a pattern in the query.</li>
                </ul>
          </div>
      </td></tr>
      </table>      
   </div>
   </fieldset>

   <div class="searchInfo all">        

      <div class="summary">
         <img border="0" id="b1" src="images/blastButton.jpg" class="blastbutton"  alt="BLAST"  mouseovImg="images/blastButtonOver.jpg" mouseoutImg="images/blastButton.jpg" mousedownImg="images/blastButtonDown.jpg" mouseupImg="images/blastButtonOver.jpg"  />
     </div>        

     <div class="searchsummary">
         <div class="openNewWin all">
            <span class="infoTitle">Search</span>
            <span class="dbInfo"></span>      
            <span class="infoTitle">using</span>
            <span class="progInfo">Blastp</span>    
            <span class="progDescr">(protein-protein BLAST) </span>                      
            <br />
            <input class="newwin" type="checkbox" name="NEWWIN" id="nw1" form="searchForm" winType="random"  />
            <label class="inlineLabel all" for="nw1">Show results in a new window</label>
         </div>
     </div>        
   </div> 

   <!-- Algorithm parameters: initially hidden -->
   <a href="#i" class="arrowlink hiding moreOptions" id="toggleOptions">Algorithm parameters</a>   
   <span id="diffMes">Note: Parameter values that differ from the default are highlighted in yellow</span>
   <div id="moreopts" toggle="toggleOptions"  class="hidden"> 
   

     <fieldset class="all section">
      
         <legend>General Parameters</legend>

         <label for="NUM_SEQ" class="m all">Max target sequences</label>
       
         <div class="input all">   
            <select name="MAX_NUM_SEQ" class="reset checkDef opts" id="NUM_SEQ" defVal="100" >               
               <option value="10"  >10</option>
               <option value="50"  >50</option>
               <option value="100" selected="selected" >100</option>
               <option value="250"  >250</option>
               <option value="500"  >500</option>
               <option value="1000"  >1000</option>
               <option value="5000"  >5000</option>
               <option value="10000"  >10000</option>
               <option value="20000"  >20000</option>
            </select><br />
            <span class="help">Select the maximum number of aligned sequences to display</span>            
            <a class="helplink hiding" title="help" id="maxTargetSeqHelp" href="#"><span>[?]</span></a>
            <p toggle="maxTargetSeqHelp" class="helpbox hidden">
            Maximum number of aligned sequences to display 
            (the actual number of alignments may be greater than this).            
            </p>
         </div>
   
    
         <label class="m blastp psiBlast phiBlast blastn megaBlast discoMegablast">Short queries</label>
         
    
         <div class="input blastp psiBlast phiBlast blastn megaBlast discoMegablast">

         
            <input type="checkbox" name="SHORT_QUERY_ADJUST" class="reset" id="adjparam" checked="checked"  />
            <label class="right inlinelabel" for="adjparam">Automatically adjust parameters for short input sequences</label>
            <a class="helplink" title="help" href="#" id="autoAdj"><span>[?]</span></a>
            <p toggle="autoAdj" class="helpbox hidden">
            Automatically adjust word size and other parameters to improve results for short queries.
            </p>
         </div>
        
    
         <label for="expect" class="m all">Expect threshold</label>
    
    
         <div class="input all">
            <input name="EXPECT" id="expect" class="reset checkDef opts" size="10" type="text" value="10" defVal="10" />            
            <a class="helplink hiding" title="help" id="expectHelp" href="#"><span>[?]</span></a>
            <p toggle="expectHelp" class="helpbox  hidden">
            Expected number of chance matches in a random model. 
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#expect" target="helpWin" >more...</a>
            </p>
         </div>
        
    
         <label for="wordsize" class="m all">Word size</label>
   
         
         <div class="input all">
            <select name="WORD_SIZE" id="wordsize" class="reset checkDef opts" defVal="3" ><option value="2"   >2</option>
<option value="3"  class="Deflt" selected="selected" >3</option></select>            
            <a class="helplink hiding" title="help" id="wordsizeHelp" href="#"><span>[?]</span></a>
            <p toggle="wordsizeHelp" class="helpbox  hidden">
            The length of the seed that initiates an alignment.
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#wordsize" target="helpWin" >more...</a>
            </p>
         </div>
        
      </fieldset>
         
      <!-- Note: Fieldset must contain the UNION of all program-related classes of its
                 contents; otherwise, it hides elements that would be otherwise shown -->
      <!-- <fieldset class="blastp blastn tblastn phi psi"> -->
      <fieldset class="blastp psiBlast phiBlast blastx tblastx tblastn blastn megaBlast discoMegablast">
         <legend>Scoring Parameters</legend>

         <!-- <label for="matrixName" class="m blastp phi psi tblastn tblastx">Matrix</label> -->
         <label for="matrixName" class="m blastp psiBlast phiBlast blastx tblastx tblastn">Matrix</label>
   
         <!-- <div class="input blastp phi psi tblastn tblastx"> -->   
         <div class="input blastp psiBlast phiBlast blastx tblastx tblastn">               
            <!--<select name="MATRIX_NAME" id="matrixName" onchange="updateGapcostProt(this);"> -->
            <select name="MATRIX_NAME" id="matrixName" class="reset checkDef opts" defVal="BLOSUM62">
                           <option value="PAM30"  >PAM30</option>
                           <option value="PAM70"  >PAM70</option>
                           <option value="BLOSUM80"  >BLOSUM80</option>
                           <option value="BLOSUM62" selected="selected" >BLOSUM62</option>
                           <option value="BLOSUM45"  >BLOSUM45</option>
                        </select>                       
                        <a class="helplink hiding" title="help" id="matrixHelp" href="#"><span>[?]</span></a>
            <p toggle="matrixHelp" class="helpbox hidden">
            Assigns a score for aligning pairs of residues, and determines overall alignment score.
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#Matrix" target="helpWin" >more...</a>
            </p>
                        <br />
         </div>

         <!-- <label for="Select3" class="m blastn mega disco">Match/Mismatch Scores</label> -->
         <label for="matchscores" class="m blastn megaBlast discoMegablast">Match/Mismatch Scores</label>
        
         <!-- <div class="input blastn mega disco"> -->
         <div class="input blastn megaBlast discoMegablast">
            <select name="MATCH_SCORES" id="matchscores" class="reset checkDef opts" defVal=""> 
                           <option value="1,-2"  >1,-2</option>
                           <option value="1,-3"  >1,-3</option>                  
                           <option value="1,-4"  >1,-4</option>                  
                           <option value="2,-3"  >2,-3</option>                  
                           <option value="4,-5"  >4,-5</option>                  
                           <option value="1,-1"  >1,-1</option>    
                        </select>                       
                        <a class="helplink hiding" title="help" id="matchScoresHelp" href="#"><span>[?]</span></a>
            <p toggle="matchScoresHelp" class="helpbox hidden">
            Reward and penalty for matching and mismatching bases.
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#Reward-penalty" target="helpWin" >more...</a>
            </p>
         </div>
         
        
         <!-- <label for="Select4" class="m all">Gap Costs</label> -->
         <label for="gapcosts" class="m blastp psiBlast phiBlast blastx tblastn blastn megaBlast discoMegablast">Gap Costs</label>
        
         <!-- <div class="input all"> -->
         <div class="input blastp psiBlast phiBlast blastx tblastn blastn megaBlast discoMegablast">
            <select name="GAPCOSTS" id="gapcosts" class="reset checkDef opts" defVal="11 1" ><!-- default depends on MatchScores selected - doen in javascript -->
            <option value = "9 2"   >Existence:  9 Extension: 2</option>	
<option value = "8 2"   >Existence:  8 Extension: 2</option>	
<option value = "7 2"   >Existence:  7 Extension: 2</option>	
<option value = "12 1"  >Existence: 12 Extension: 1</option>	
<option value = "11 1" class="Deflt" selected="selected" >Existence: 11 Extension: 1</option>	
<option value = "10 1"  >Existence: 10 Extension: 1</option>		                                
            </select>            
            <a class="helplink hiding" title="help" id="gapcostsHelp" href="#"><span>[?]</span></a>
            <p toggle="gapcostsHelp" class="helpbox hidden">
            Cost to create and extend a gap in an alignment.  
            
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#Matrix" target="helpWin" >more...</a>
            </p>
         </div>
   
         <!-- <label for="Select7" class="m blastp phi psi tblastn">Compositional adjustments</label> -->
         <label for="compbasedstat" class="m blastp psiBlast tblastn">Compositional adjustments</label>
   
         <!-- <div class="input blastp phi psi tblastn"> -->
         <div class="input blastp psiBlast tblastn">
            <select name="COMPOSITION_BASED_STATISTICS" id="compbasedstat" class="reset checkDef opts" defVal="2">
               <option value="0"  >No adjustment</option>
                           <option value="1"  >Composition-based statistics</option>
                           <option value="2" selected="selected" >Conditional compositional score matrix adjustment</option>
                           <option value="3"  >Universal compositional score matrix adjustment</option>
            </select>            
            <a class="helplink hiding" title="help" id="compAdjHelp" href="#"><span>[?]</span></a>
            <p toggle="compAdjHelp" class="helpbox hidden">
            Matrix adjustment method to compensate for amino acid composition of sequences.
            <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#compositional_adjustments" target="helpWin" >more...</a>
            </p>
         </div>
         
      </fieldset>

      <!-- <fieldset class="all"> -->
      <fieldset class="all section">
         <legend>Filters and Masking</legend>

         <!-- <label class="m all">Filter</label> -->
         <label class="m all">Filter</label>
   
         <!-- <div class="input all"> -->
         <div class="input all">
               <div class="all"><input type="checkbox" name="FILTER" value="L" id="fil_l"  class="reset checkDef opts" defVal="unchecked"/>
               <label class="right inlinelabel" for="fil_l">Low complexity regions</label>               
               <a class="helplink hiding" title="help" id="lowCompFilterHelp" href="#"><span>[?]</span></a>               
                                <p toggle="lowCompFilterHelp" class="helpbox hidden">
                                Mask regions of low compositional complexity 
                                that may cause spurious or misleading results.
                                <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#filter" target="helpWin" >more...</a>
                                </p></div>
                
               <div class="blastn megaBlast discoMegablast">
               <input type="checkbox" name="FILTER" value="R" id="fil_r"  class="reset checkDef opts" defVal="" /><!-- default depends on database selected - done in JS -->
               <label class="right inlinelabel" for="fil_r">Species-specific repeats for: </label>               
               <select id="repeats" name="REPEATS">
                  <option value="repeat_9606" id="rhf"  >Human</option>
                  <option value="repeat_9989" id="rmf"  >Rodents</option>
                  <option value="repeat_3702"  >Arabidopsis</option>
                  <option value="repeat_4530"  >Rice</option>
                                  <option value="repeat_40674"  >Mammals</option>
                                  <option value="repeat_4751"  >Fungi</option>
                                  <option value="repeat_6239"  >C. elegans</option>
                                  <option value="repeat_7165"  >A. gambiae</option>
                                  <option value="repeat_7955"  >Zebrafish</option>
                                  <option value="repeat_7227"  >Fruit fly</option>
               </select>               
               <a class="helplink hiding" title="help" id="repeatsFilterHelp" href="#"><span>[?]</span></a>
                           <p toggle="repeatsFilterHelp" class="helpbox hidden">
                           Mask repeat elements of the specified species that may 
                           lead to spurious or misleading results.
                           <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#filter" target="helpWin" >more...</a>
                           </p>
               </div>            
            
         </div>
         
         <!-- <label class="m all">Mask</label> -->
         <label class="m all">Mask</label>

         <!-- <div class="input all"> -->
         <div class="input all">
               <div class="all"><input type="checkbox" name="FILTER" value="m" id="fil_m"  class="reset checkDef opts" defVal="unchecked" />
               <label class="right inlinelabel" for="fil_m">Mask for lookup table only</label>               
               <a class="helplink hiding" title="help" id="lookupMaskFilterHelp" href="#"><span>[?]</span></a>
                                <p toggle="lookupMaskFilterHelp" class="helpbox hidden">
                                Mask query while producing seeds used to scan database, 
                                but not for extensions.
                                <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#filter" target="helpWin" >more...</a>
                                </p></div>                                             
                                <div class="all">
               <input type="checkbox" name="LCASE_MASK" id="lcm"  class="reset checkDef opts" defVal="unchecked" />
               <label for="lcm" class="right inlinelabel">Mask lower case letters</label>               
               <a class="helplink hiding" title="help" id="lowerCaseMaskFilterHelp" href="#"><span>[?]</span></a>
                                <p toggle="lowerCaseMaskFilterHelp" class="helpbox hidden">
                                Mask any letters that were lower-case in the FASTA input.
                                <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#filter" target="helpWin" >more...</a>
                                </p>
                                </div>         
            

         </div>
      </fieldset>

      <!-- <fieldset class="disco"> -->
      <fieldset class="discoMegablast section">

         <legend>Discontiguous Word Options</legend>
    
         <!-- <label for="TEMPLATE_LENGTH" class="m disco">Template length</label> -->
         <label for="templlength" class="m discoMegablast">Template length</label>

         <!-- <div class="input disco">   -->
         <div class="input discoMegablast">   
            <select name="TEMPLATE_LENGTH" id="templlength" class="reset checkDef opts" defVal="">
               <option value="0"  >None</option>
                                <option value="16"  >16</option>
                                <option value="18"  >18</option>
                                <option value="21"  >21</option>      
            </select>            
            <a class="helplink hiding" title="help" id="templateLengthHelp" href="#"><span>[?]</span></a>
                    <p toggle="templateLengthHelp" class="helpbox hidden">
                    Total number of bases in a seed that ignores some positions.
                    <a href="http://www.ncbi.nlm.nih.gov/blast/discontiguous.shtml" target="helpWin" >more...</a>
                    </p>
         </div>
    
         <!-- <label for="TEMPLATE_TYPE" class="m disco">Template type</label> -->
         <label for="templtype" class="m discoMegablast">Template type</label>

         <!-- <div class="input disco">   -->
         <div class="input discoMegablast">   
            <select name="TEMPLATE_TYPE" id="templtype" size="0" class="reset checkDef opts" defVal="" >
               <option value="0"  >Coding</option>
                           <option value="1"  >Maximal</option>
                           <option value="2"  >Two templates</option>
            </select>            
            <a class="helplink hiding" title="help" id="templateTypeHelp" href="#"><span>[?]</span></a>
                    <p toggle="templateTypeHelp" class="helpbox hidden">
                    Specifies which bases are ignored in scanning the database.
                    <a href="http://www.ncbi.nlm.nih.gov/blast/discontiguous.shtml" target="helpWin" >more...</a>
                    </p>
         </div>
      </fieldset>

      <!-- <fieldset class="psi phi"> -->
      <fieldset class="psiBlast phiBlast section">

         <legend>PSI/PHI BLAST</legend>

            <!-- PSI/PHI blast-->           
            
            <div class="psiBlast phiBlast" id="promptPSSM" >
            <label for="promptPSSM" class="m psiBlast phiBlast ">PSSM</label>             
                        <div class="input  psiBlast phiBlast checkDef opts" defVal="hide" id="savedPSSM">PSSM is uploaded...
                        <a href="#" id="cpssm">Clear</a>
                        </div>                  
                        </div>
                        
                        <div class="psiBlast phiBlast" id="uplPSSM">
            <label for="pssm" class="m psiBlast phiBlast">Upload PSSM 
            <span class="hint">Optional</span></label>
            <div class="input psiBlast phiBlast">
               <input name="PSSM" id="pssm" type="file" />
               <a class="helplink hiding" title="help" id="pssmHelp" href="#"><span>[?]</span></a>
               <p toggle="pssmHelp" class="helpbox hidden">
               Upload a Position Specific Score Matrix (PSSM) that you
               previously downloaded from a PSI-BLAST iteration. You may
               search a different database than that used to generate the
               PSSM, but you must use the same query.
               <a href="http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#pssm" target="helpWin" >more...</a>
               </p>               
            </div>
            </div>
            
            <!-- <label for="I_THRESH" class="m psi">PSI-BLAST Threshold</label> -->
            <label for="I_THRESH" class="m psiBlast phiBlast">PSI-BLAST Threshold</label>
            <!--  <div class="input psi"> -->
            <div class="input psiBlast phiBlast">
               <input name="I_THRESH" id="I_THRESH" class="reset" value="" type="text" title="Threshold for PSI-BLAST iteration" defVal="0.005"/>
               <a class="helplink hiding" title="help" id="iThreshHelp" href="#"><span>[?]</span></a>
                       <p toggle="iThreshHelp" class="helpbox hidden">
                       
                  Set the statistical significance threshold 
                  to include a sequence in the model used by PSI-BLAST
                  to create the PSSM on the next iteration.
                       </p>               
            </div>
      </fieldset>

   <div class="searchInfo all" id="srchBottom">

      <div class="summary">
         <img border="0" id="b2" src="images/blastButton.jpg" class="blastbutton"  alt="BLAST"  mouseovImg="images/blastButtonOver.jpg" mouseoutImg="images/blastButton.jpg" mousedownImg="images/blastButtonDown.jpg" mouseupImg="images/blastButtonOver.jpg"  />
     </div>        

     <div class="searchsummary">
         <div id="openNewWin2" class="openNewWin all">
            <span class="infoTitle">Search</span>
            <span class="dbInfo"></span>      
            <span class="infoTitle">using</span>
            <span class="progInfo">Blastp</span>    
            <span class="progDescr">(protein-protein BLAST) </span>                      
            <br />
            <input class="newwin" type="checkbox" name="NEWWIN" id="nw2" form="searchForm" winType="random"  />
            <label class="inlineLabel all" for="nw2">Show results in a new window</label>
         </div>
     </div>        
   </div> 

      </div><!-- /#moreopts -->
      
<!-- hidden fields for deafult formatting params -->
<span class="all">
<input class="reset" name="SHOW_OVERVIEW" id="SHOW_OVERVIEW" type="hidden" value="on" defVal="true" />
<input class="reset" name="SHOW_LINKOUT" id="SHOW_LINKOUT" type="hidden" value="on" defVal="true" />
<input class="reset" name="GET_SEQUENCE" id="GET_SEQUENCE" type="hidden" value="on" defVal="true" />
<input class="reset" name="FORMAT_OBJECT" id="FORMAT_OBJECT" type="hidden" value="Alignment" defVal="Alignment"/>
<input class="reset" name="FORMAT_TYPE" id="FORMAT_TYPE" type="hidden" value="HTML" defVal="HTML"/>
<input class="reset" name="ALIGNMENT_VIEW" id="ALIGNMENT_VIEW" type="hidden" value="Pairwise" defVal="Pairwise"/>
<input class="reset" name="MASK_CHAR" id="MASK_CHAR" type="hidden" value="2" defVal="2"/>
<input class="reset" name="MASK_COLOR" id="MASK_COLOR" type="hidden" value="1" defVal="1"/>
<input class="reset" name="DESCRIPTIONS" id="DESCRIPTIONS" type="hidden" value="100"defVal="100" />
<input class="reset" name="ALIGNMENTS" id="ALIGNMENTS" type="hidden" value="100" defVal="100" /> 
<input class="reset" name="NEW_VIEW" id="NEW_VIEW" type="hidden" value="" defVal="true"/>
<input class="reset" name="OLD_BLAST" id="OLD_BLAST" type="hidden" value="false" defVal="false"/>

<input class="reset" name="NCBI_GI" id="NCBI_GI" type="hidden" value="" defVal="false"/>
<input class="reset" name="SHOW_CDS_FEATURE" id="SHOW_CDS_FEATURE" type="hidden" value="" defVal="false"/>
<input class="reset" name="NUM_OVERVIEW" id="NUM_OVERVIEW" type="hidden" value="100"/ defVal="100"> 
<input class="reset" name="FORMAT_EQ_TEXT" id="FORMAT_EQ_TEXT" type="hidden" value=""/>
<input class="reset" name="FORMAT_ORGANISM" id="FORMAT_ORGANISM" type="hidden" value=""/> 
<input class="reset" name="EXPECT_LOW" id="EXPECT_LOW" type="hidden" value=""/> 
<input class="reset" name="EXPECT_HIGH" id="EXPECT_HIGH" type="hidden" value=""/>
<input name="QUERY_INDEX" id="QUERY_INDEX" type="hidden" value="0"/>
<!-- Those are located in CURR_SAVED_OPTIONS
<input name="RUN_PSIBLAST" type="hidden" value=""/>
<input name="I_THRESH" type="hidden" value=""/> 
-->
</span>      

</div><!--/#sopts--> 
</div>
<!-- </div> -->
                      
<!-- hidden fields for all program types -->
<input name="CLIENT" type="hidden" value="web" />
<input name="SERVICE" type="hidden" value="plain" />
<input name="CMD" type="hidden" value="request"/>
<input name="PAGE" type="hidden" id="page" value="Proteins"/>
<input name="PROGRAM" type="hidden" value="blastp" id="program" />
<input name="MEGABLAST" type="hidden" id="runMegablast" value="" />
<input name="RUN_PSIBLAST" type="hidden" id="runPSI" value="" />


<!-- Hidden Field for Disco Megablast -->
<input name="TWO_HITS" id="twoHits" type="hidden" value="" />
<!-- <input name="RESULTS_PAGE_TARGET" type="hidden" id="resPageTarget" value="" /> -->


<!-- hidden fields that depend on the program type -->
<input name="CDD_SEARCH" type="hidden" value="on" id="cddSearch" />
<input name="ID_FOR_PSSM" type="hidden" value="" id="dpssm"/>

<input type="hidden" id="blastpProg" value="Blastp" />
<input type="hidden" id="blastpDescr" value="(protein-protein BLAST) " />
<input type="hidden" id="psiBlastProg" value="PSI-BLAST" />
<input type="hidden" id="psiBlastDescr" value="(Position-Specific Iterated BLAST)" />
<input type="hidden" id="phiBlastProg" value="PHI-BLAST" />
<input type="hidden" id="phiBlastDescr" value="(Pattern Hit Initiated BLAST)" />



<!-- hidden fields for the progs that are changed by radiobuttons -->
<input name="SAVED_PSSM" type="hidden" value="" />
<!--<input name="STEP_NUMBER" type="hidden" value="" />-->

<!-- hidden field inicating what type of blasnFamily or blastpFamily selected -->
<input name="SELECTED_PROG_TYPE" id="selectedProg" type="hidden" value="blastp"/>

<!-- hidden field inicating that saved search data is displayed -->
<input name="SAVED_SEARCH" id="savedSearch" type="hidden" value=""/>

<!-- hidden field inicating if Specialized blast was used like Trace Archive -->
<input name="BLAST_SPEC" id="blastSpec" type="hidden" value=""/>

<!-- hidden fields for cgi context params -->
<input name="QUERY_BELIEVE_DEFLINE" type="hidden" value=""/>
<!-- next three for TAXID_LIST = "on" -->
<input name="DB_DIR_PREFIX" type="hidden" value=""/>

            


<!-- hidden fields for saving params passed by user - for validation purposes -->
<input name="USER_DATABASE" type="hidden" value="" id="USER_DATABASE"/>
<input name="USER_WORD_SIZE" type="hidden" value="" id="USER_WORD_SIZE"/>
<input name="USER_MATCH_SCORES" type="hidden" value="" id="USER_MATCH_SCORES"/>
<input name="USER_FORMAT_DEFAULTS" type="hidden" value="" id="USER_FORMAT_DEFAULTS"/>

<input name="NO_COMMON" type="hidden" value="" id="noCommon"/>

<!-- the field that will keep the number of differemces from default params the first for all
the second only for options -->
<input name="NUM_DIFFS" type="hidden" value="0" id="NUM_DIFFS"/> 
<input name="NUM_OPTS_DIFFS" type="hidden" value="0" id="NUM_OPTS_DIFFS"/> 

<!-- the field that will keep uniq object id for saved deafults object -->
<input name="UNIQ_DEFAULTS_NAME" type="hidden" value="" id="UNIQ_DEFAULTS_NAME"/> 


<input name="PAGE_TYPE" type="hidden" value="BlastSearch"/> 
<!-- place where original word_size, gapcosts will be saved -->        
<div id="savedLists">
<select id="gapcosts_BLOSUM62"><option value = "9 2"   >Existence:  9 Extension: 2</option>	
<option value = "8 2"   >Existence:  8 Extension: 2</option>	
<option value = "7 2"   >Existence:  7 Extension: 2</option>	
<option value = "12 1"  >Existence: 12 Extension: 1</option>	
<option value = "11 1" class="Deflt" selected="selected" >Existence: 11 Extension: 1</option>	
<option value = "10 1"  >Existence: 10 Extension: 1</option>		</select>
<select id="gapcosts_PAM30"><option value = "7 2"  >Existence:  7 Extension: 2</option>			
<option value = "6 2"  >Existence:  6 Extension: 2</option>		
<option value = "5 2"  >Existence:  5 Extension: 2</option>		
<option value = "10 1" >Existence: 10 Extension: 1</option>		
<option value = "9 1" class="Deflt"  >Existence: 9 Extension: 1</option>		
<option value = "8 1"  >Existence: 8 Extension: 1</option></select>
<select id="gapcosts_PAM70"><option value = "8 2"  >Existence:  8 Extension: 2</option>	
<option value = "7 2"  >Existence:  7 Extension: 2</option>			
<option value = "6 2"  >Existence:  6 Extension: 2</option>			
<option value = "11 1" >Existence: 11 Extension: 1</option>	
<option value = "10 1" class="Deflt"  >Existence: 10 Extension: 1</option>		
<option value = "9 1"  >Existence: 9 Extension: 1</option></select>
<select id="gapcosts_BLOSUM80"><option value = "8 2"   >Existence:  8 Extension: 2</option>	
<option value = "7 2"   >Existence:  7 Extension: 2</option>			
<option value = "6 2"   >Existence:  6 Extension: 2</option>			
<option value = "11 1"   >Existence: 11 Extension: 1</option>	
<option value = "10 1" class="Deflt"  >Existence: 10 Extension: 1</option>		
<option value = "9 1"   >Existence: 9 Extension: 1</option>		</select>
<select id="gapcosts_BLOSUM45"><option value = "13 3"   >Existence: 13 Extension: 3</option>	
<option value = "12 3"   >Existence: 12 Extension: 3</option>	
<option value = "11 3"   >Existence: 11 Extension: 3</option>	
<option value = "10 3"   >Existence: 10 Extension: 3</option>	
<option value = "15 2" class="Deflt"  >Existence: 15 Extension: 2</option>	
<option value = "14 2"   >Existence: 14 Extension: 2</option>	
<option value = "13 2"   >Existence: 13 Extension: 2</option>	
<option value = "12 2"   >Existence: 12 Extension: 2</option>	
<option value = "19 1"   >Existence: 19 Extension: 1</option>	
<option value = "18 1"   >Existence: 18 Extension: 1</option>	
<option value = "17 1"   >Existence: 17 Extension: 1</option>	
<option value = "16 1"   >Existence: 16 Extension: 1</option>		</select>
<input name="USER_DEFAULT_PROG_TYPE" id="userDefaultprog" type="hidden" value="blastp"/>
<input name="USER_DEFAULT_MATRIX" id="userDefaultMatrix" type="hidden" value=""/>
</div>
</form>
</div><!--/#content-->


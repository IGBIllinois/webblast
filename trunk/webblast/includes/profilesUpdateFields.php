<?php
$queryProfiles = "SELECT * FROM blast_profiles WHERE userid=".$_SESSION['userid'];
$profiles = $sqlDataBase->query($queryProfiles); 


echo "<script>";
echo "var profilesArr = [];\n";
echo "profilesArr[\"21\"] = new Array(\"200\",\"0\",\"0\",\"10\",\"0\",\"T\",\"-1\",\"-1\",\"0\",\"F\",\"-3\",\"1\",\"3\",\"3\",\"0\",\"T\",\"1\",\"1\",\"2\",\"F\",\"BLOSUM62\",\"0\",\"0\",\"0\",\"0\",\"3\",\"0\",\"\",\"F\",\"0\",\"0\",\"\",\"F\",\"\",\"0\",\"0\",\"0\",\"0\",\"D\",\"p d i e o v b a\");"; 
foreach($profiles as $id=>$profile)
{
	echo "profilesArr[\"".$profile['id']."\"] = new Array(\"".$profile['chunksize']."\",\"".$profile['blastid']."\",\"".$profile['dbid']."\",\"".$profile['e']."\",\"".$profile['m']."\",\"".$profile['FU']."\",\"".$profile['GU']."\",\"".$profile['EU']."\",\"".$profile['XU']."\",\"".$profile['IU']."\",\"".$profile['q']."\",\"".$profile['r']."\",\"".$profile['v']."\",\"".$profile['b']."\",\"".$profile['f']."\",\"".$profile['g']."\",\"".$profile['QU']."\",\"".$profile['DU']."\",\"".$profile['a']."\",\"".$profile['JU']."\",\"".$profile['MU']."\",\"".$profile['WU']."\",\"".$profile['z']."\",\"".$profile['KU']."\",\"".$profile['YU']."\",\"".$profile['SU']."\",\"".$profile['TU']."\",\"".$profile['l']."\",\"".$profile['UU']."\",\"".$profile['y']."\",\"".$profile['ZU']."\",\"".$profile['RU']."\",\"".$profile['n']."\",\"".$profile['LU']."\",\"".$profile['AU']."\",\"".$profile['w']."\",\"".$profile['t']."\",\"".$profile['BU']."\",\"".$profile['CU']."\",\"".$profile['paramsenabled']."\");\n";
}

?>

function OnSelectProfile(profileSelect)
{
	var selectedId = profileSelect.value;
	ResetToDefault();
	UpdateParameters(selectedId);	
}

function ResetToDefault()
{
	UncheckAll(document.MainBlastFormSimple.elements['params[]']);
}

function UpdateParameters(selectedId)
{
	var chunkSize = document.getElementById('chunkSize');
	selectOptionByValue(chunkSize,profilesArr[selectedId][0]);
	var p = document.getElementById('p');
	selectOptionByValue(p,profilesArr[selectedId][1]);
	fill_capital(p.selectedIndex);
	var d = document.getElementById('d');
	selectOptionByValue(d,profilesArr[selectedId][2]);
	var e = document.getElementById('e');
	e.value=profilesArr[selectedId][3];
	var m = document.getElementById('m');
	selectOptionByValue(m,profilesArr[selectedId][4]);
	var FU = document.getElementById('FU');
	FU.value = profilesArr[selectedId][5];
	var GU = document.getElementById('GU');
	GU.value = profilesArr[selectedId][6];
	var EU = document.getElementById('EU');
	EU.value = profilesArr[selectedId][7];
	var XU = document.getElementById('XU');
	XU.value = profilesArr[selectedId][8];
	var IU = document.getElementById('IU');
	IU.checked = (profilesArr[selectedId][9]==='T') ? true : false;
	var q = document.getElementById('q');
	q.value = profilesArr[selectedId][10];
	var r = document.getElementById('r');
	r.value = profilesArr[selectedId][11];
	var v = document.getElementById('v');
	v.value = profilesArr[selectedId][12];
	var b = document.getElementById('b');
	b.value = profilesArr[selectedId][13];
	var f = document.getElementById('f');
	f.value = profilesArr[selectedId][14];
	var g = document.getElementById('g');
	g.checked = (profilesArr[selectedId][15]==='T') ? true : false;
	var QU = document.getElementById('QU');
	QU.value = profilesArr[selectedId][16];
	var DU = document.getElementById('DU');
	DU.value = profilesArr[selectedId][17];
	var a = document.getElementById('a');
	a.value = profilesArr[selectedId][18];
	var JU = document.getElementById('JU');
	JU.checked = (profilesArr[selectedId][19]==='T') ? true : false;
	var MU = document.getElementById('MU');
	selectOptionByValue(MU,profilesArr[selectedId][20]);
	var WU = document.getElementById('WU');
	WU.value = profilesArr[selectedId][21];
	var z = document.getElementById('z');
	z.value=profilesArr[selectedId][22];
	var KU = document.getElementById('KU');
	KU.value = profilesArr[selectedId][23];
	var YU = document.getElementById('YU');
	YU.value = profilesArr[selectedId][24];
	var SU = document.getElementById('SU');
	SU.value = profilesArr[selectedId][25];
	var TU = document.getElementById('TU');
	TU.checked = (profilesArr[selectedId][26]==='T') ? true : false;
	var l = document.getElementById('l');
	l.value = profilesArr[selectedId][27];
	var UU = document.getElementById('UU');
	UU.checked = (profilesArr[selectedId][28]==='T') ? true : false;
	var y = document.getElementById('y');
	y.value = profilesArr[selectedId][29];
	var ZU = document.getElementById('ZU');
	ZU.value = profilesArr[selectedId][30];
	var RU = document.getElementById('RU');
	RU.value = profilesArr[selectedId][31];
	var n = document.getElementById('n');
	n.checked = (profilesArr[selectedId][32]==='T') ? true : false;
	var LU = document.getElementById('LU');
	LU.value = profilesArr[selectedId][33];
	var AU = document.getElementById('AU');
	AU.value = profilesArr[selectedId][34];
	var w = document.getElementById('w');
	w.value = profilesArr[selectedId][35];
	var t = document.getElementById('t');
	t.value = profilesArr[selectedId][36];
	var BU = document.getElementById('BU');
	BU.value = profilesArr[selectedId][37];
	var CU = document.getElementById('CU');
	CU.value = profilesArr[selectedId][38];
	
	var paramIter;
	var parameters = profilesArr[selectedId][39];
	parameters = parameters.split(" ");
	for (paramIter in parameters)
	{
		var parameter = parameters[paramIter];
		document.getElementById(parameter + "_box").checked = true;
	}
}

function selectOptionByValue(selObj, val){
    var A= selObj.options, L= A.length;
    while(L){
        if (A[--L].value== val){
            selObj.selectedIndex= L;
            L= 0;
        }
    }
}

function UncheckAll(field)
{
	for(i=0; i<field.length;i++)
	{
		field[i].checked=false;
	}
}

<?php
echo "</script>";
?>


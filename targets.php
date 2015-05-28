<?   
// targets.php
// Lists all KOIs and displays links to individual KOI pages, 
// finding charts, and observing notes

include 'config.php';
include 'coord_convert.php';
$sort = $_GET["sort"];

// Get number of targets
$query0 = "select id from kois where koi != ''";
$res0 = mysql_query($query0) or die(mysql_error());
$num = mysql_num_rows($res0);
?>

<html>
<body>

<center>
<table width=97% border=0>
<tr>
<td width=200>
<h4>KOIs (<?=$num?>)</h4>
</td>

<td align=right valign=top>
<form action="targets.php" method="GET">
Sort by:
<select name="sort" onchange="this.form.submit();">
<option value="koi" <? if ($sort=="koi") echo ' selected';?>>Star</option>
<option value="kid" <? if ($sort=="kid") echo ' selected';?>>KIC ID</option>
</select>
</form>
</td>
</tr>
</table>

<br>

<table width=97% border=1>
<tr>
<td rowspan=2 height=30>KOI Host<br>Star</td>
<td rowspan=2>KIC ID</td>
<td rowspan=2>Kepler Name</td>
<td colspan=5 align=center height=30 nowrap># KOIs</td>
<td colspan=3 align=center>Position (J2000)</td>
<td rowspan=2>Kep<br>mag</td>
<td rowspan=2>Ks<br>mag</td>
<td colspan=2 align=center># Derived Params</td>
<td colspan=3 align=center># Observations</td>
<td rowspan=2>Observing<br>Notes</td>
<td rowspan=2>Last Modified</td>
</tr>

<tr>
<td width=2% height=30>CP</td>
<td width=2%>PC</td>
<td width=2%>FP</td>
<td width=2%>ND</td>
<td width=2%>TOT</td>
<td>RA</td>
<td>Dec</td>
<td>Finder Chart</td>
<td width=4%>Stellar</td>
<td width=4%>Planet</td>
<td width=5%>Spec</td>
<td width=5%>Img</td>
<td width=5%>RV</td>
</tr>

<?
$order = " order by cast(targets.koi as signed)";
switch ($sort) {
	case "koi":
	$order = " order by cast(targets.koi as signed)";   
    break;
    case "kid":
    $order = " order by (kid=''), cast(kid as signed)";
    break;
}

$query0 = "select id, kid, koi, kep, num, num_pc, num_fp, 
num_cf, num_nl, ra, dc, mag_kep, mag_ks, num_stel, num_plan, 
num_rv, num_spec, num_img, targets.last_mod, targets.last_mod_by 
from kois";
$query0 = $query0.$order;
$res0 = mysql_query($query0) or die(mysql_error());
while ($row = mysql_fetch_array($res0)) {

$tid = $row["id"];
$kid = $row["kid"];
$koi = $row["koi"];
$kep = $row["kep"];
$num = $row["num"];
$numpc = $row["num_pc"];
$numfp = $row["num_fp"];
$numcf = $row["num_cf"];
$numnd = $row["num_nl"];
$ra_dec = $row["ra"];
$dec_dec = $row["dc"];
$kmag = $row["mag_kep"];
$smag = $row["mag_ks"];
$num_stel = $row["num_stel"];
$num_plan = $row["num_plan"];
$num1 = $row["num_rv"];
$num2 = $row["num_spec"];
$num3 = $row["num_img"];
$lastmod = $row["last_mod"];
$lastmodby = $row["last_mod_by"];

// Convert decimal coords to sexagesimal
$coords = coords_dec2sex($ra_dec, $dec_dec);
$ra = $coords[0];
$dec = $coords[1];

echo "<tr>
<td><a target=\"$koi\" href=\"edit_target.php?id=$koi\">$koi</a></td>
<td>$kid&nbsp;</td>
<td>$kep&nbsp;</td>

<td>$numcf</td>
<td>$numpc</td>
<td>$numfp</td>
<td>$numnd</td>
<td>$num</td>

<td>$ra</td>
<td>$dec</td>
<td><a href=\"\">view</a></td>

<td>$kmag</td>
<td>$smag</td>
   
<td>$num_stel</td>
<td>$num_plan</td>
   
<td>$num2</td>
<td>$num3</td>
<td>$num1</td>
		 
<td><a target=\"notes$tid\" href=\"edit_obsnotes.php?id=$koi\">edit</a></td>
<td>$lastmod<br>by $lastmodby</td>
</tr>";
}
?>

</table>
</center>
</body>
</html>



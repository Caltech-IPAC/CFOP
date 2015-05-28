<?   
// view_spect.php
// Displays all spectroscopy observations of all KOIs by all users

include 'config.php';
$sort = $_GET["sort"];

// Get total number of observations
$query = "select count(tid), count(distinct tid) from spect_obs";
$res = mysql_query($query);
$row = mysql_fetch_array($res);
$num1 = number_format($row[0]);
$num2 = number_format($row[1]);
?>

<html>
<body>

<center>
<table width=90% border=0>
<tr>
<td>
<h3>Spectroscopic Observations</h3>
<h4><?=$num1?> total observations; <?=$num2?> unique stars</h4>
</td>

<td align=right>
<form action="view_spect.php" method="GET">
Sort by:
<select name="sort" onchange="this.form.submit();">
<option value="koi" <? if ($sort=="koi") echo ' selected';?>>KOI</option>
<option value="tel" <? if ($sort=="tel") echo ' selected';?>>Telescope</option>
</select>
</form>
</td>
</tr>
</table>


<table width=90% border=1>
<tr>
<td>KOI</td>
<td>Telescope</td>
<td>Instrument</td>
<td>Spectral<br>resolution (R)</td>
<td>Wavelength<br>coverage</td>
<td>SNR/resolution</td>
<td>SNR wavelength</td>
<td>Flux<br>Calibrated?</td>
<td>Wave<br>Calibrated?</td>
<td>RV Type</td>
<td>Observation<br>date (UT)</td>
<td>Notes</td>
<td>User</td>
</tr>

<?
$order = " order by cast(koi as signed)";
switch ($sort) {
	case "koi":
	$order=" order by cast(koi as signed)";
	break;	
	case "tel":
	$order=" order by tel, cast(koi as signed)";
	break;
}
	
$query = "select spect_obs.id, kois.koi, tel, inst, specres, wavcov_start, 
wavcov_end, wavunits, snrres, snrwave, obsdate, fluxcal, wavecal, absrv, 
spect.notes, user from kois, spect_obs where kois.id = spect_obs.tid";
$query = $query . $order;
$res = mysql_query($query) or die('spect error');

while ($row = mysql_fetch_array($res)) {
$id = $row["id"];
$koi = $row["koi"];
$kid = $row["kid"];
$stand = $row["stand"];
$sptel = $row["tel"];
$spinst = $row["inst"];
$spres = $row["specres"];
$spw_stt = $row["wavcov_start"];
$spw_end = $row["wavcov_end"];
$spw_unt = $row["wavunits"];
$spsnr1 = $row["snrres"];
$spsnr2 = $row["snrwave"];
$spdate = $row["obsdate"];
$spflux = $row["fluxcal"];
$spwave = $row["wavecal"];
$spabsrv = $row["absrv"];
$spdate2 = $row["date"];
$spuser = $row["user"];
$spnotes = $row["notes"];
list ($spobsdate1, $spobsdate2) = split(" ", $spdate);

echo "<tr>
<td><a target=\"$id\" href=\"edit_target.php?id=$koi\">$koi</a></td>
<td>$sptel</td>
<td>$spinst</td>
<td>$spres</td>	
<td>$spw_stt to $spw_end";

if ($spw_unt == "nm") echo " nm"; 
if ($spw_unt == "microns") echo " &#956;m"; 
if ($spw_unt == "Angstroms") echo " &#8491;"; 

echo "</td>	
<td>$spsnr1&nbsp;</td>
<td>$spsnr2&nbsp;</td>
<td>$spflux&nbsp;</td>
<td>$spwave&nbsp;</td>
<td>$spabsrv&nbsp;</td>

<td>$spobsdate1";
if ($spobsdate2 != "00:00:00") echo " ".$spobsdate2;

echo "</td>
<td>$spnotes</td>
<td>$spuser</td>
</tr>";
}
?>

</table>
</center>
</body>
</html>


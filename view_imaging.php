<?   
// view_imaging.php
// Displays all imaging observations of all KOIs by all users

include 'config.php';
$sort = $_GET["sort"];

// Get total number of observations
$query = "select count(tid), count(distinct tid) from img_obs";
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
<h3>Imaging Observations</h3>
<h4><?=$num1?> total observations; <?=$num2?> unique stars</h4>
</td>

<td align=right>
<form action="view_imaging.php" method="GET">
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
<td>Filter</td>
<td>Pixel scale<br>(arcsec)</td>
<td>Estimated<br>PSF</td>
<td>Estimated<br>Contrast</td>
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

$query = "select kois.koi, img_obs.id, tel, inst, filter, pixscale, psf, 
cont_mag, cont_sep, obsdate, img_obs.notes, user from kois, img_obs 
where kois.id = imag_obs.tid";
$query = $query . $order; 
$res = mysql_query($query);

while ($row = mysql_fetch_array($res)) {
$id = $row["id"];
$koi = $row["koi"];
$tel = $row["tel"];
$inst = $row["inst"];
$filter = $row["filter"];
$pixscale = $row["pixscale"];
$psf = $row["psf"];
$cont_mag = $row["cont_mag"];
$cont_sep = $row["cont_sep"];
$date = $row["obsdate"];
$notes = $row["notes"];
$user = $row["user"];

echo "<tr>
<td><a target=\"$id\" href=\"edit_target.php?id=$koi\">$koi</a></td>
<td>$tel</td>
<td>$inst</td>
<td>$filter</td>
<td>$pixscale</td>	
<td>$psf</td>
<td>";

echo "&#916;" . $cont_mag . " mag @ " . $cont_sep . '"';

echo "</td>
<td>$date</td>
<td>$notes</td>
<td>$user</td>
</tr>";
}
?>

</table>
</center>
</body>
</html>



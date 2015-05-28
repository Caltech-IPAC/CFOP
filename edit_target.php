<?   
// edit_target.php
// Displays parameters and files for an individual KOI

include 'config.php';
include 'coord_convert.php';
$koi = $_GET['id'];

// Get general info from targets table
$query0 = "select id, kid, kep, ra, dc, mag_kep, mag_ks, num_rv, num_spec, 
num_img, last_mod, last_mod_by from kois where koi='$koi'";
$res0 = mysql_query($query0) or die(mysql_error());
while ($row0 = mysql_fetch_array($res0)) {
$id = $row0["id"];
$kid = $row0["kid"];
$kep = $row0["kep"];
$ra_dec = $row0["ra"];
$dec_dec = $row0["dc"];
$mkp = $row0["mag_kep"];
$mks = $row0["mag_ks"];
$num1 = $row0["num_spec"];
$num2 = $row0["num_img"];
$num3 = $row0["num_rv"];
$lm = $row0["last_mod"];
$lmb = $row0["last_mod_by"];
}

// Convert decimal coords to sexagesimal, galactic
$coords = coords_dec2sex($ra_dec, $dec_dec);
$ra = $coords[0];
$dec = $coords[1];

// Convert RA decimal hrs to RA decimal degrees
$ra_dec_deg = $ra_dec*15;

// Galactic coords
$coords_gal = coords_dec2gal($ra_dec, $dec_dec);
$ra_gal = $coords_gal[0];
$dec_gal = $coords_gal[1];

// Get number of sources within 4 arcsec from multiplicity tables
$query941 = "select count(id) from mult_ukirt where tid = $id and dist <= 4";
$res941 = mysql_query($query941);
$row941 = mysql_fetch_array($res941);
$m4num = $row941[0];

$query943 = "select count(id) from mult_ubv where tid = $id and dist <= 4";
$res943 = mysql_query($query943);
$row943 = mysql_fetch_array($res943);
$m4num2 = $row943[0];

// Get number of sources within 10 arcsec from multiplicity tables
$query942 = "select count(id) from mult_ukirt where tid = $id and dist <= 10";
$res942 = mysql_query($query942);
$row942 = mysql_fetch_array($res942);
$m10num = $row942[0];

$query944 = "select count(id) from mult_ubv where tid = $id and dist <= 10";
$res944 = mysql_query($query944);
$row944 = mysql_fetch_array($res944);
$m10num2 = $row944[0];

// Get KOI ID(s) and name(s)
$koiids = array();
$koinames = array();
$koidisp = array();
$koikep = array();
$query00 = "select id, koi, disposition, kepname from planets where tid=$id order by koi";
$res00 = mysql_query($query00);
while ($row00 = mysql_fetch_array($res00)) {
$koiids[] = $row00['id'];
$koinames[] = $row00['koi'];
$koidisp[] = $row00['disposition'];
$koikep[] = $row00['kepname'];
}

// Get number of observing notes
$oq = "select count(id) from obs_notes where target_id=$id";
$or = mysql_query($oq);
$ow = mysql_fetch_array($or);
$num_obsnotes = $ow[0];

// Get Possible FP status
$ebquery = "select fpos, notes from fpos where tid=$id order by id DESC limit 1";
$ebres = mysql_query($ebquery);
$ebrow = mysql_fetch_array($ebres);
$ebin = $ebrow[0];
$ebnotes = $ebrow[1];

// Get Nearby Companion status
$ncquery = "select ncomp, notes from ncomp where tid=$id order by id DESC limit 1";
$ncres = mysql_query($ncquery);
$ncrow = mysql_fetch_array($ncres);
$ncom = $ncrow[0];
$ncnotes = $ncrow[1];
?>


<html>
<body>

<center>

<table width=94% border=1 cellpadding=3>
<tr>
<td>
<h4>KOI <?=$koi?></h4>
<em>Last modified by <?=$lmb?><br><?=$lm?></em>
</td>

<td>
<a href="edit_obsnotes.php?id=<?=$koi?>">Open Observing Notes (<?=$num_obsnotes?>)</a>
</td>

<td width=90 align=right valign=top rowspan=2>
<a target="ukjpg" href="">
<img src="" alt="UKIRT JPG" height="90" width="90">
<br>
UKIRT J-band
</a>
</td>

<td width=90 align=right valign=top rowspan=2>
<a target="fcjpg" href="">
<img src="" alt="FC JPG" height="90" width="90">
<br>
Finder chart
</a>
</td>
</tr>

<tr>
<td colspan=2>Jump to:
<a href="#transit">Transit Params</a> &nbsp; 
<a href="#orbit">Orbital Params</a> &nbsp; 
<a href="#planet">Planet Params</a> &nbsp; 
<a href="#stellar">Stellar Params</a> &nbsp;
<a href="#mags">Magnitudes</a> &nbsp; 
<a href="#obs">Observations Summary</a> &nbsp;
<a href="#mult">Nearby Stars</a> &nbsp;
<a href="#files">Files</a>

<br>

Download:
<a target="down_data" href="">Text file of this page</a>
&nbsp;
<a target="down_files" href="">All files (tar)</a> 
&nbsp;
<a target="down_afiles" href="">All files (zip)</a> 
</td>
</tr>
</table>


<br>


<table width=94% border=0>
<tr>
<td width=49% valign=top>


<table width=100% border=1>

<tr>
<td colspan=4 align=center>Summary of Stellar Parameters</td>
</tr>

<tr>
<td>Star Name</td>   
<td colspan=3>KOI <?=$koi?>, KIC <?=$kid?>
<? if ($kep != '' ) echo ", $kep"; ?>
</td>
</tr>

<tr>
<td>Planet Name(s)</td>   
<td colspan=3>

<?
for ($k=0; $k<count($koinames); $k++) {
echo $koinames[$k]. " (";
if ($koidisp[$k] == 'PC') echo "Planetary Candidate"; 
if ($koidisp[$k] == 'FP') echo "False Positive"; 
if ($koidisp[$k] == 'NULL') echo "Not set"; 
if ($koidisp[$k] == 'Confirmed') echo "Confirmed Planet"; 
if ($koidisp[$k] == 'NOT DISPOSITIONED') echo "Not Dispositioned"; 
echo ")";
if ($koikep[$k] != '') echo ", ".$koikep[$k];
echo "<br>";
}
?>
</td>
</tr>

<tr>
<td>Possible False Positive?</td>   
<td colspan=3>
<?
if ($ebin == 'Y') echo "Yes";
if ($ebin == 'N') echo "No";
if ($ebnotes != '') echo ": " . $ebnotes;
?>
</td>
</tr>

<tr>
<td>Possible Nearby Companion?</td>   
<td colspan=3>
<?
if ($ncom == 'Y') echo "Yes";
if ($ncom == 'N') echo "No";
if ($ncnotes != '') echo ": " . $ncnotes;
?>
</td>
</tr>

<tr>
<td rowspan=3>Position<br>(J2000)</td>   
<td>RA/Dec (h:m:s)</td>
<td><?=$ra?></td>
<td><?=$dec?></td>
</tr>

<tr>
<td>RA/Dec (deg)</td>
<td><?=$ra_dec?></td>
<td><?=$dec_dec?></td>
</tr>

<tr>
<td>Gal Long/Lat (deg)</td>
<td><?=$dec_gal?></td>
<td><?=$ra_gal?></td>
</tr>

<tr>
<td>Kepler mag</td>
<td colspan=3><?=$mkp?></td>
</tr>


<?
// Get default V magnitude
$vq = "select magv from magv where tid=$id";
$vr = mysql_query($vq) or die(mysql_error());
$vw = mysql_fetch_array($vr);
$vmag = $vw['magv'];

// Get default stellar parameters
$query571 = "select teff, teff_error, logg, logg_error, radius, radius_error, 
vsini, vsini_error, feh, feh_error, mass, mass_error, dens, dens_error, notes, 
date, user from stellar_params where tid=$id";
$res571 = mysql_query($query571) or die(mysql_error());
while ($row571 = mysql_fetch_array($res571)) {
$teff = $row571['teff'];
$teff_error = $row571['teff_error'];  
$logg = $row571['logg'];
$logg_error = $row571['logg_error'];
$radius = $row571['radius'];
$radius_error = $row571['radius_error'];
$vsini = $row571['vsini'];
$vsini_error = $row571['vsini_error'];    
$feh = $row571['feh']; 
$feh_error = $row571['feh_error'];      
$mass = $row571['mass'];
$mass_error = $row571['mass_error'];    
$dens = $row571['dens'];
$dens_error = $row571['dens_error'];    
$snotes = $row571['notes'];
$sdate = $row571['date'];
$suser = $row571['user'];
}
?>

<tr>
<td>V mag</td>
<td colspan=3><?=$vmag?></td>
</tr>

<tr>
<td>Ks mag</td>
<td colspan=3><?=$mks?></td>
</tr>

<tr>
<td>Teff (K)</td>
<td colspan=3>
<?
echo $teff;
if ($teff_error != '') echo " &plusmn; $teff_error";
?>
</td>
</tr>
<tr>
<td>log(g)</td>
<td colspan=3>
<?
echo $logg;
if ($logg_error != '') echo " &plusmn; $logg_error";
?>
</td>
</tr>
<tr>
<td>Radius (R_Sun)</td>
<td colspan=3>
<? 
echo $radius;
if ($radius_error != '') echo " &plusmn; $radius_error";
?>
</td>
</tr>
<tr>
<td>Mass (M_Sun)</td>
<td colspan=3>
<?
echo $mass;
if ($mass_error != '') echo " &plusmn; $mass_error";
?>
</td>
</tr>
<tr>
<td>Vsini (km/s)</td>
<td colspan=3>
<? 
echo $vsini;
if ($vsini_error != '') echo " &plusmn; $vsini_error";
?>
</td>
</tr>
<tr>
<td>[Fe/H]</td>
<td colspan=3>
<?
echo $feh;
if ($feh_error != '') echo " &plusmn; $feh_error";
?>
</td>
</tr>
<tr>
<td>Density (g/cm3)</td>
<td colspan=3>
<?
echo $dens;
if ($dens_error != '') echo " &plusmn; $dens_error";
?>
</td>
</tr>
</table>


</td>



<td width=1%>&nbsp;</td>

<td width=50% valign=top>


<table width=100% border=1>
<tr>
<td colspan=6 align=center>Summary of Planet Parameters</td>
</tr>

<tr>
<td>KOI</td>   
<td>Radius (R_Earth)</td>
<td>Transit Period (days)</td>
<td>Transit Depth (mmag)</td>
<td>SMA (AU)</td>
<td>Eq Temp (K)</td>
</tr>

<?	
// Loop through KOIs
for ($i=0; $i<count($koiids); $i++) {
$koin = $koinames[$i];
$koid = $koiids[$i];

// Get default transit period, depth
$query56 = "select period, period_error, depth, depth_error,
notes, user, date from transit_params where koi=$koid and final='Y'";
$res56 = mysql_query($query56) or die(mysql_error());
while ($row56 = mysql_fetch_array($res56)) {
$tp = $row56["period"];
$tpe = $row56["period_error"];
$dep = $row56["depth"];
$depe = $row56["depth_error"];
}

// Get default orbital period, SMA
$query57 = "select period, period_error, smaxis, smaxis_error,
notes, user, date from orbital_params where koi=$koid and final='Y'";
$res57 = mysql_query($query57) or die(mysql_error());
while ($row57 = mysql_fetch_array($res57)) {
$op = $row57["period"];
$ope = $row57["period_error"];
$sm = $row57["smaxis"];
$sme = $row57["smaxis_error"];
$ornotes = $row57["notes"];
$ordate = $row57["date"];
$oruser = $row57["user"];
}

// Get default planet radius, equilibrium temp
$query570 = "select radius, radius_error, temp, temp_error, 
notes, date, user from planet_params where koi=$koid and final='Y'";
$res570 = mysql_query($query570) or die(mysql_error());
while ($row570 = mysql_fetch_array($res570)) {
$rad = $row570["radius"];
$rade = $row570["radius_error"];
$temp = $row570["temp"];
$tempe = $row570["temp_error"];
$pnotes = $row570["notes"];
$pdate = $row570["date"];
$puser = $row570["user"];
}	
?>	
	
<tr>	
<td><?=$koin?></td>
<td>
<?=$rad?>
<? if ($rade != '') echo " &plusmn; $rade";?>
</td>

<td>
<?=$tp?>
<? if ($tpe != '') echo " &plusmn; $tpe";?>
</td>

<td>
<?=$dep?>
<? if ($depe != '') echo " &plusmn; $depe";?>
</td>

<td>
<?=$sm?>
<? if ($sme != '') echo " &plusmn; $sme";?>
</td>

<td>
<?=$temp?>
<? if ($tempe != '') echo " &plusmn; $tempe";?>
</td>

</tr>

<?
} // end loop through KOIs
?>

</table>

<br>

<table width=49% border=1 style="display: inline-table;">
<tr>
<td colspan=5 align=center># Observations</td>
</tr>

<tr>
<td width=60%>Spectroscopic</td>
<td><?=$num1?></td>
</tr>

<tr>
<td>Imaging</td>
<td><?=$num2?></td>
</tr>

<tr>
<td>Radial Velocity</td>
<td><?=$num3?></td>
</tr>
</table>


<table width=50% border=1 style="display: inline-table;">
<tr>
<td colspan=5 align=center># Nearby Stars</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>UKIRT</td>
<td>UBV</td>
</tr>

<tr>
<td>within 4 arcsec</td>
<td><?=$m4num?></td>
<td><?=$m4num2?></td>
</tr>

<tr>
<td>within 10 arcsec</td>
<td><?=$m10num?></td>
<td><?=$m10num2?></td>
</tr>
</table>

<br><br>

<table width=100% border=1 style="clear: both;">
<tr>
<td align=center>Links Outside of CFOP</td>
</tr>

<tr>
<td>Exoplanet Archive:
<a target="exarch<?=$id?>"
href="http://exoplanetarchive.ipac.caltech.edu/cgi-bin/DisplayOverview/nph-DisplayOverview?objname=KOI-<?=$koi?>&type=KEPLER_HOST">
Overview page</a>
|
<a target="exarchltcrv<?=$id?>"
href="http://exoplanetarchive.ipac.caltech.edu/cgi-bin/ICETimeSeriesViewer/nph-ICEtimeseriesviewer?inventory_mode=id_single&idtype=source&id=<?=$kid?>&dataset=Kepler">
Light curve viewer</a> 
| 
<a target="eadvr" href="http://exoplanetarchive.ipac.caltech.edu/cgi-bin/ExoOverview/nph-ExoOverview?objname=KOI-<?=$koi?>&dvr&type=KEPLER_HOST">DV reports</a>
</td>
</tr>

<tr>
<td>Exoplanet Archive transit predictor:
<?
for ($i=0; $i<count($koiids); $i++) {
echo "<a target=\"$koinames[$i]\"
href=\"http://exoplanetarchive.ipac.caltech.edu/cgi-bin/TransitTables/nph-visibletbls?dataset=transits&koi&sname=$koi1&phase=primary,secondary,quadrature\">
$koinames[$i]</a>";
if ($i < count($koiids)-1) echo " | ";
}
?>
</td>
</tr>

<tr>
<td>
<a target="irsa"
href="http://irsa.ipac.caltech.edu/applications/finderchart/#id=Hydra_finderchart_finder_chart&DoSearch=true&subsize=0.08333333400000001&thumbnail_size=medium&sources=DSS,SDSS,twomass,WISE&UserTargetWorldPt=<?=$ra_dec?>;<?=$dec_dec?>;EQ_J2000&SimpleTargetPanel.field.resolvedBy=nedthensimbad&dss_bands=poss1_blue,poss1_red,poss2ukstu_blue,poss2ukstu_red,poss2ukstu_ir&SDSS_bands=u,g,r,i,z&twomass_bands=j,h,k&wise_bands=1,2,3,4&projectId=finderchart&searchName=finder_chart&startIdx=0&pageSize=0&shortDesc=Finder%20Chart&isBookmarkAble=true&isDrillDownRoot=true&isSearchResult=true">IRSA Finder Chart</a>
</td>
</tr>

<tr>
<td>
<a target="simbad"
href="http://simbad.u-strasbg.fr/simbad/sim-id?Ident=KOI-<?=$koi?>&NbIdent=1&Radius=2&Radius.unit=arcmin&submit=submit+id">
SIMBAD for KOI-<?=$koi?></a> (if available)
</td>
</tr>

<tr>
<td>
exoplanets.org: 
<?
for ($i=0; $i<count($koiids); $i++) {
$orgurl = "http://exoplanets.org/detail/KOI_".$koinames[$i];
echo "<a target=\"org\" href=\"$orgurl\">$koinames[$i]</a>";
if ($i < count($koiids)-1) echo " | ";
}
?>
 (if available)
</td>
</tr>

<tr>
<td>
<a target="mast"
href="http://archive.stsci.edu/kepler/data_search/search.php?action=Search&ktc_kepler_id=<?=$kid?>">
MAST lightcurve</a>
</td>
</tr>

<tr>
<td colspan=3>
<a target="koa"
href="https://koa.ipac.caltech.edu/cgi-bin/KOA/nph-KOA?instrument_hi=hires&filetype=science&spt_obj=objname&targname=<?=$koi?>&mode_hi1=iodine-in&mode_hi2=iodine-out">
Keck Observatory Archive</a>
</td>
</tr>

<tr>
<td>
<a target="ebin" href="http://keplerebs.villanova.edu/overview/?k=<?=$kid?>">Eclipsing Binary Catalog</a>
(if available)
</td>
</tr>
</table>
	
</td>
</tr>
</table>



<br><br>



<!-- Transit Parameters -->

<?
// Get total number of transit params
$koistr = implode(",", $koiids); 
$ntp = "select count(id) from transit_params where koi in ($koistr)";
$rtp = mysql_query($ntp);
$rowtp = mysql_fetch_array($rtp);
$numtp = $rowtp[0];
?>
<a name="transit"></a>

<table width=94% border=1>
<tr>
<td colspan=13 align=center>
Transit Parameters (<?=$numtp?>)
<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>KOI</th>
<th>Epoch<br>(BJD)</th>
<th>Period<br>(days)</th>
<th>Depth<br>(mmag)</th>
<th>Depth<br>(ppm)</th>
<th>Duration<br>(hrs)</th>
<th>R_planet/R_star</th>
<th>Fitted Stellar Density<br>(g/cm3)
<th>Planetary<br>Fit Type</th>
<th>Notes</th>
<th>Date</th>
<th>User</th>
<th>Preferred<br>Value</th>
</tr>

<?
// Get KOIs
for ($i=0; $i<count($koiids); $i++) {
$koin = $koinames[$i];
$koid = $koiids[$i];

// Get transit info for this KOI
$query55 = "select id, epoch, epoch_error, period, period_error, 
depth, depth_error, duration, duration_error, dist, dist_error, 
dens, dens_error, fit, final, notes, user, date from transit_params where 
koi=$koid order by (final='Y') DESC, date DESC";
$res55 = mysql_query($query55) or die(mysql_error());
while ($row55 = mysql_fetch_array($res55)) {

$tepoch = '';
$tepoch_error = '';
$period = '';
$period_error = '';
$depth = '';
$depth_error = '';
$depth_ppm = '';
$depth_ppm_error = '';
$duration = '';
$duration_error = '';
$dist = '';
$dist_error = '';
$dens = '';
$dens_error = '';
$fit = '';
$trfinal = '';
$trnotes = '';
$trdate = '';
$truser = '';

$trid = $row55["id"];
$tepoch = $row55["epoch"];
$tepoch_error = $row55["epoch_error"];
$period = $row55["period"];
$period_error = $row55["period_error"];
$depth = $row55["depth"];
$depth_error = $row55["depth_error"];
$duration = $row55["duration"];
$duration_error = $row55["duration_error"];
$dist = $row55["dist"];
$dist_error = $row55["dist_error"];
$dens = $row55["dens"];
$dens_error = $row55["dens_error"];
$fit = $row55["fit"];
$trfinal = $row55["final"];
$trnotes = $row55["notes"];
$trdate = $row55["date"];
$truser = $row55["user"];

// Convert depth from mmag to ppm
if ($depth != '') {
$depth_ppm = 1000000*(1 - pow(10, -0.4*$depth/1000) );
$depth_ppm = round($depth_ppm);
}
if ($depth_error != '') {
$depth_ppm_error = 1000000*(1 - pow(10, -0.4*$depth_error/1000) );
$depth_ppm_error = round($depth_ppm_error);
}
?>

<tr>
<td><?=$koin?></td>

<td><?=$tepoch?><br>
<? if ($tepoch_error != '') echo "&plusmn;$tepoch_error";?>
</td>

<td><?=$period?><br>
<? if ($period_error != '') echo "&plusmn;$period_error";?>
</td>

<td><?=$depth?><br>
<? if ($depth_error != '') echo "&plusmn;$depth_error";?>
</td>

<td><?=$depth_ppm?><br>
<? if ($depth_ppm_error != '') echo "&plusmn;$depth_ppm_error";?>
</td>

<td><?=$duration?><br>
<? if ($duration_error != '') echo "&plusmn;$duration_error";?>
</td>

<td><?=$dist?><br>
<? if ($dist_error != '') echo "&plusmn;$dist_error";?>
</td>

<td><?=$dens?><br>
<? if ($dens_error != '') echo "&plusmn;$dens_error";?>
</td>

<td><?=$fit?></td>
<td><?=$trnotes?>&nbsp;</td>
<td><?=$trdate?></td>
<td><?=$truser?></td>
<td><?=$trfinal?>&nbsp;</td>

<?
}
echo "</tr>";
}
echo "</table>";
?>


<br><br>



<!-- Orbital Parameters -->

<?
// Get total number of orbital params
$nop = "select count(id) from orbital_params where koi in ($koistr)";
$rop = mysql_query($nop);
$rowop = mysql_fetch_array($rop);
$numop = $rowop[0];
?>

<a name="orbit"></a>

<table width=94% border=1>
<tr>
<td colspan=11 align=center>
Orbital Parameters (<?=$numop?>)
<a href="#">Add new</a>
</td>
</tr>
	
<tr>
<th>KOI</th>
<th>Period<br>(days)</th>
<th>Semi-major Axis<br>(AU)</th>
<th>Inclination<br>(deg)</th>
<th>Eccentricity</th>
<th>Longitude of Periastron<br>(deg)</th>
<th>Epoch of Periastron<br>(HJD)</th>
<th width=160>Notes</th>
<th width=130>Date</th>
<th width=90>User</th>
<th width=60>Preferred<br>Value</th>
</tr>

<?
// Loop through KOIs
for ($i=0; $i<count($koiids); $i++) {
$koin = $koinames[$i];
$koid = $koiids[$i];

// Get orbital params for this KOI
$op = '';
$ope = '';
$sm = '';
$sme = '';
$inc = '';
$ince = '';
$ecc = '';
$ecce = '';
$lp = '';
$lpe = '';
$ep = '';
$epe = '';
$orfinal = '';
$ornotes = '';
$ordate = '';
$oruser = '';
$o = 1;

$query57 = "select id, period, period_error, smaxis, smaxis_error,
inclin, inclin_error, eccent, eccent_error, longper, longper_error,
epochper, epochper_error, final, notes, user, date from orbital_params 
where koi=$koid order by (final='Y') DESC, date DESC";
$res57 = mysql_query($query57) or die(mysql_error());
while ($row57 = mysql_fetch_array($res57)) {
$oid = $row57["id"];
$op = $row57["period"];
$ope = $row57["period_error"];
$sm = $row57["smaxis"];
$sme = $row57["smaxis_error"];
$inc = $row57["inclin"];
$ince = $row57["inclin_error"];
$ecc = $row57["eccent"];
$ecce = $row57["eccent_error"];
$lp = $row57["longper"];
$lpe = $row57["longper_error"];
$ep = $row57["epochper"];
$epe = $row57["epochper_error"];
$orfinal = $row57["final"];
$ornotes = $row57["notes"];
$ordate = $row57["date"];
$oruser = $row57["user"];
?>

<tr>
<td>
<?=$koin?>
</td>

<td><?=$op?><br>
<? if ($ope != '') echo "&plusmn;$ope";?>
</td>

<td><?=$sm?><br>
<? if ($sme != '') echo "&plusmn;$sme";?>
</td>

<td><?=$inc?><br>
<? if ($ince != '') echo "&plusmn;$ince";?>
</td>

<td><?=$ecc?><br>
<? if ($ecce != '') echo "&plusmn;$ecce";?>
</td>

<td><?=$lp?><br>
<? if ($lpe != '') echo "&plusmn;$lpe";?>
</td>

<td><?=$ep?><br>
<? if ($epe != '') echo "&plusmn;$epe";?>
</td>

<td><?=$ornotes?>&nbsp;</td>
<td><?=$ordate?></td>
<td><?=$oruser?></td>
<td><?=$orfinal?>&nbsp;</td>

<?
}
echo "</tr>";
}
?>
	
</table>


<br><br>



<!-- Planet Parameters -->

<?
// Get total number of planet params 
$npp = "select count(id) from planet_params where koi in ($koistr)";
$rpp = mysql_query($npp);
$rowpp = mysql_fetch_array($rpp);
$numpp = $rowpp[0];
?>

<a name="planet"></a>

<table width=94% border=1>
<tr>
<td colspan=12 align=center>
Planet Parameters (<?=$numpp?>)
<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>KOI</th>
<th>Radius<br>(R_Earth)</th>
<th>Mass<br>(M_Earth)</th>
<th>Msin(i)<br>(M_Earth)</th>
<th>Equilibrium Temperature<br>(K)</th>
<th>Insolation Flux<br>(flux_Earth)</th>
<th>Notes</th>
<th>Date</th>
<th>User</th>
<th>Preferred<br>Value</th>
</tr>
	
<?
// Get KOIs
for ($i=0; $i<count($koiids); $i++) {
$koin = $koinames[$i];
$koid = $koiids[$i];

// Get planet params for this KOI
$rad = '';
$rade = '';
$mass = '';
$masse = '';
$msin = '';
$msine = '';
$temp = '';
$tempe = '';
$ins = '';
$inse = '';
$pfinal = '';
$pnotes = '';
$pdate = '';
$puser = '';

$query570 = "select id, radius, radius_error, mass, mass_error,
msini, msini_error, temp, temp_error, insol, insol_error, final,
notes, date, user from planet_params where koi=$koid 
order by (final='Y') DESC, date DESC";
$res570 = mysql_query($query570) or die(mysql_error());
while ($row570 = mysql_fetch_array($res570)) {
$pid = $row570["id"];
$rad = $row570["radius"];
$rade = $row570["radius_error"];
$mass = $row570["mass"];
$masse = $row570["mass_error"];
$msin = $row570["msini"];
$msine = $row570["msini_error"];
$temp = $row570["temp"];
$tempe = $row570["temp_error"];
$ins = $row570["insol"];
$inse = $row570["insol_error"];
$pfinal = $row570["final"];
$pnotes = $row570["notes"];
$pdate = $row570["date"];
$puser = $row570["user"];
?>

<tr>
<td>
<?=$koin?>
</td>

<td>
<?=$rad?>
<br>
<? if ($rade != '') echo "&plusmn;$rade";?>
</td>

<td>
<?=$mass?>
<br>
<? if ($masse != '') echo "&plusmn;$masse";?>
</td>

<td>
<?=$msin?>
<br>
<? if ($msine != '') echo "&plusmn;$msine";?>
</td>

<td>
<?=$temp?>
<br>
<? if ($tempe != '') echo "&plusmn;$tempe";?>
</td>

<td>
<?=$ins?>
<br>
<? if ($inse != '') echo "&plusmn;$inse";?>
</td>

<td><?=$pnotes?>&nbsp;</td>
<td><?=$pdate?></td>
<td><?=$puser?></td>
<td><?=$pfinal?>&nbsp;</td>

<?
}
echo "</tr>";
}
?>
	
</table>


<br><br>



<!-- STELLAR Params -->

<?
// Get total number of stellar parameters
$query12 = "select count(id) from stellar_params where tid=$id";
$res12 = mysql_query($query12);
$row12 = mysql_fetch_array($res12);
$stellar_num = $row12[0];
?>

<a name="stellar"></a>

<table width=94% border=1>
<tr>
<td colspan=17 align=center>
Stellar Parameters (<?=$stellar_num?>)
<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>Teff<br>(K)</th>
<th>log(g)</th>
<th>Radius<br>(R_Sun)</th>
<th>Vsini<br>(km/s)</th>
<th>Spectral<br>Type</th>
<th>logR'HK</th>
<th>[Fe/H]</th>
<th>Dist<br>(pc)</th>
<th>Mass<br>(M_Sun)</th>
<th>Density<br>(g/cm3)</th>
<th>Rot Per<br>(days)</th>
<th>Luminosity<br>(L_Sun)</th>
<th>S-index</th>
<th width=160>Notes</th>
<th width=130>Date</th>
<th width=90>User</th>
<th width=60>Preferred<br>Value</th>
</tr>

<?
$teff = '';
$teff_error = '';  
$logg = '';
$logg_error = '';
$radius = '';
$radius_error = '';
$vsini = '';
$vsini_error = '';    
$sptype = '';      
$logr = '';
$logr_error = '';      
$feh = ''; 
$feh_error = '';      
$dist = '';
$dist_error = '';      
$mass = '';
$mass_error = '';    
$dens = '';
$dens_error = '';    
$rotper = '';
$rotper_error = '';    
$lum = ''; 
$lum_error = '';
$sindex = '';
$sindex_error = '';
$sfinal = '';
$snotes = '';
$sdate = '';
$suser = '';

$query571 = "select id, teff, teff_error, logg, logg_error, radius, radius_error, 
vsini, vsini_error, sptype, logr, logr_error, feh, feh_error, dist, dist_error,
mass, mass_error, dens, dens_error, rotper, rotper_error, lum, lum_error, sindex, 
sindex_error, final, notes, date, user from stellar_params where tid=$id order by 
(final='Y') DESC, date DESC";
$res571 = mysql_query($query571) or die(mysql_error());
while ($row571 = mysql_fetch_array($res571)) {
$sid = $row571['id'];
$teff = $row571['teff'];
$teff_error = $row571['teff_error'];  
$logg = $row571['logg'];
$logg_error = $row571['logg_error'];
$radius = $row571['radius'];
$radius_error = $row571['radius_error'];
$vsini = $row571['vsini'];
$vsini_error = $row571['vsini_error'];    
$sptype = $row571['sptype'];
$logr = $row571['logr'];
$logr_error = $row571['logr_error'];      
$feh = $row571['feh']; 
$feh_error = $row571['feh_error'];      
$dist = $row571['dist'];
$dist_error = $row571['dist_error'];      
$mass = $row571['mass'];
$mass_error = $row571['mass_error'];    
$dens = $row571['dens'];
$dens_error = $row571['dens_error'];    
$rotper = $row571['rotper'];
$rotper_error = $row571['rotper_error'];    
$lum = $row571['lum']; 
$lum_error = $row571['lum_error'];
$sindex = $row571['sindex'];
$sindex_error = $row571['sindex_error'];
$sfinal = $row571['final'];
$snotes = $row571['notes'];
$sdate = $row571['date'];
$suser = $row571['user'];
?>

<tr>
<td>
<?=$teff?>
<br>
<? if ($teff_error != '') echo "&plusmn;$teff_error";?>
</td>
<td>
<?=$logg?>
<br>
<? if ($logg_error != '') echo "&plusmn;$logg_error";?>
</td>
<td>
<?=$radius?>
<br>
<? if ($radius_error != '') echo "&plusmn;$radius_error";?>
</td>
<td>
<?=$vsini?>
<br>
<? if ($vsini_error != '') echo "&plusmn;$vsini_error";?>
</td>
<td>
<?=$sptype?>
&nbsp;
</td>
<td>
<?=$logr?>
<br>
<? if ($logr_error != '') echo "&plusmn;$logr_error";?>
</td>
<td>
<?=$feh?>
<br>
<? if ($feh_error != '') echo "&plusmn;$feh_error";?>
</td>
<td>
<?=$dist?>
<br>
<? if ($dist_error != '') echo "&plusmn;$dist_error";?>
</td>
<td>
<?=$mass?>
<br>
<? if ($mass_error != '') echo "&plusmn;$mass_error";?>
</td>
<td>
<?=$dens?>
<br>
<? if ($dens_error != '') echo "&plusmn;$dens_error";?>
</td>
<td>
<?=$rotper?>
<br>
<? if ($rotper_error != '') echo "&plusmn;$rotper_error";?>
</td>
<td>
<?=$lum?>
<br>
<? if ($lum_error != '') echo "&plusmn;$lum_error";?>
</td>
<td>
<?=$sindex?>
<br>
<? if ($sindex_error != '') echo "&plusmn;$sindex_error";?>
</td>

<td><?=$snotes?></td>
<td><?=$sdate?></td>
<td><?=$suser?></td>
<td><?=$sfinal?>&nbsp;</td>

<?
echo "</tr>";
}
?>

</table>


<br><br>



<!-- Magnitudes -->

<?
// Get total # of all magnitudes
$totalmags = 0;
foreach ($mags as $mag=>$magname) {
$query0 = "select count(id) from $mag where tid=$id";
$res0 = mysql_query($query0);
$row0 = mysql_fetch_array($res0);
$totalmags += $row0[0];
}
?>

<a name="mags"></a>


<table width=94% border=1>
<tr>
<td colspan=6 align=center>
Magnitudes (<?=$totalmags?>)
<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>Band</th>
<th>Value</th>
<th>Notes</th>
<th width=130>Date</th>
<th width=90>User</th>
<th width=60>Preferred<br>Value</th>
</tr>

<?
foreach ($mags as $mag=>$magname) {

// Get all magnitudes
$mag_val = '';
$mag_error = '';
$mag_final = '';
$mag_notes = '';
$mag_date = '';
$mag_user = '';

$query1 = "select id, $mag, error, final, notes, date, user from $mag 
where tid=$id order by (final='Y') DESC, date DESC";
$res1 = mysql_query($query1);
while ($row1 = mysql_fetch_array($res1)) {

$mid = $row1["id"];
$mag_val = $row1["$mag"];
$mag_error = $row1["error"];
$mag_final = $row1["final"];
$mag_notes = $row1["notes"];
$mag_date = $row1["date"];
$mag_user = $row1["user"];
?>

<tr>
<td>
<?=$magname?>
</td>

<td><?=$mag_val?>
<? if ($mag_error != '') echo " &plusmn; $mag_error";?>
</td>
<td><?=$mag_notes?>&nbsp;</td>
<td><?=$mag_date?></td>
<td><?=$mag_user?></td>
<td><?=$mag_final?></td>

<?
}
echo "</tr>";
}
echo "</table>";
?>


	
<br><br>



<!-- Spectroscopy Table -->

<a name="obs"></a>


<table width=94% border=1>
<tr>
<td colspan=13 align=center>
Spectroscopic Observations (<?=$num1?>)

<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>Telescope</th>
<th>Instrument</th>
<th>Spectral<br>resolution (R)</th>
<th>Wavelength<br>coverage</th>
<th>SNR/res</th>
<th>SNR<br>wave</th>
<th>Flux<br>Calibrated?</th>
<th>Wave<br>Calibrated?</th>
<th>RV Type</th>
<th>Observation<br>date (UT)</th>
<th>Notes</th>
<th>Date</th>
<th>User</th>
</tr>

<?
// Get spectroscopy observations for this star
$query1233 = "select id, tel, inst, specres, wavcov_start, wavcov_end, wavunits, 
snrres, snrwave, obsdate, fluxcal, wavecal, absrv, notes, date, user from spect_obs
where tid=$id order by date DESC";
$res1233 = mysql_query($query1233) or die('spect error');
while ($row1233 = mysql_fetch_array($res1233)) {
$spid = $row1233["id"];
$sptel = $row1233["tel"];
$spinst = $row1233["inst"];
$spres = $row1233["specres"];
$spw_stt = $row1233["wavcov_start"];
$spw_end = $row1233["wavcov_end"];
$spw_unt = $row1233["wavunits"];
$spsnr1 = $row1233["snrres"];
$spsnr2 = $row1233["snrwave"];
$spdate = $row1233["obsdate"];
$spflux = $row1233["fluxcal"];
$spwave = $row1233["wavecal"];
$spabsrv = $row1233["absrv"];
$spdate2 = $row1233["date"];
$spuser = $row1233["user"];
$spnotes = $row1233["notes"];
list ($spobsdate1, $spobsdate2) = split(" ", $spdate);
?>

<tr>
<td><?=$sptel?></td>
<td><?=$spinst?></td>
<td><?=$spres?></td>	
<td><?=$spw_stt?> to <?=$spw_end?> 
<?
if ($spw_unt == "nm") echo " nm"; 
if ($spw_unt == "microns") echo " &#956;m"; 
if ($spw_unt == "Angstroms") echo " &#8491;"; 
?>
</td>	

<td><?=$spsnr1?>&nbsp;</td>
<td><?=$spsnr2?>&nbsp;</td>
<td><?=$spflux?>&nbsp;</td>
<td><?=$spwave?>&nbsp;</td>
<td><?=$spabsrv?>&nbsp;</td>

<td>
<?
echo $spobsdate1;
if ($spobsdate2 != "00:00:00") echo " ".$spobsdate2;
?>
</td>
<td><?=$spnotes?>&nbsp;</td>
<td><?=$spdate2?></td>
<td><?=$spuser?></td>
</tr>

<?
}
?>

</table>
<br><br>



<!-- Imaging Table -->



<table width=94% border=1>
<tr>
<td colspan=10 align=center>
Imaging Observations (<?=$num2?>)

<a href="#">Add new</a>
</td>
</tr>


<tr>
<th>Telescope</th>
<th>Instrument</th>
<th>Filter</th>
<th>Pixel scale<br>(arcsec)</th>
<th>Estimated<br>PSF</th>
<th>Estimated<br>Contrast</th>
<th>Observation<br>date (UT)</th>
<th>Notes</th>
<th>Date</th>
<th>User</th>
</tr>

<?
// Get imaging observations for this star
$query1234 = "select id, tel, inst, filter, filtcent, filtwidth, filtunits, 
pixscale, psf, cont_mag, cont_sep, obsdate, notes, date, user from img_obs 
where tid=$id order by date DESC";
$res1234 = mysql_query($query1234) or die('img error');
while ($row1234 = mysql_fetch_array($res1234)) {
$imid = $row1234["id"];
$imtel = $row1234["tel"];
$iminst = $row1234["inst"];
$imfil = $row1234["filter"];
$imfilct = $row1234["filtcent"];
$imfilwd = $row1234["filtwidth"];
$imfilun = $row1234["filtunits"];
$impix = $row1234["pixscale"];
$impsf = $row1234["psf"];
$imct1 = $row1234["cont_mag"];
$imct2 = $row1234["cont_sep"];
$imdate = $row1234["obsdate"];
$imnotes = $row1234["notes"];
$imdate2 = $row1234["date"];
$imuser = $row1234["user"];
?>

<tr>
<td><?=$imtel?></td>
<td><?=$iminst?></td>

<td nowrap>
<?
if ($imfil != '') echo $imfil;
if ($imfil != '' && $imfilct != '' && $imfilwd != '' && $imfilun != '') {
echo ": ";
}

if ($imfilct != '' && $imfilwd != '' && $imfilun != '') {
echo $imfilct." (".$imfilwd.") ";
if ($imfilun == "nm") echo "nm"; 
if ($imfilun == "microns") echo "&#956;m"; 
if ($imfilun == "Angstroms") echo "&#8491;"; 
}
?>
&nbsp;</td>	

<td><?=$impix?></td>	
<td><?=$impsf?>&nbsp;</td>
<td>
<?
if ($imct1 != '' && $imct2 != '') {
echo "&#916;" . $imct1 . " mag @ " . $imct2 . '"';
}
else echo "&nbsp;";
?>
</td>

<td><?=$imdate?></td>
<td><?=$imnotes?></td>
<td><?=$imdate2?></td>
<td><?=$imuser?></td>
</tr>

<?
}
?>

</table>



<br><br>



<!-- UKIRT Multiplicity -->


<?
// Get total number of objects 
$mq = "select count(id) from mult_ukirt where tid=$id";
$mqr = mysql_query($mq);
$mqw = mysql_fetch_array($mqr);
$nummul = $mqw[0];
?>

<a name="mult"></a>

<table width=94% border=1>
<tr>
<td colspan=17 align=center>
UKIRT Nearby Stars (<?=$nummul?>)
</td>
</tr>

<tr>
<th>RA</th>
<th>Dec</th>
<th>Distance<br>(arcsec)</th>
<th>PA<br>(deg EofN)</th>
<th>J mag</th>
<th>Kep mag</th>
<th>d_Kepmag</th>
<th>Star<br>probability</th>
<th>Galaxy<br>probability</th>
<th>Noise<br>probability</th>
<th>KIC</th>
<th>KIC_Kepmag</th>
<th>KIC_dKepmag</th>
<th>KIC_Dist</th>
<th>User</th>
</tr>

<?
// Get multiplicity
$query333 = "select ra, dc, ras, dcs, dist, pangle, jmag, jerr, kmag, dkmag,
prob_star, prob_gal, prob_noise, kic, kic_kep, kic_dkep, kic_dist, date, user 
from mult_ukirt where tid=$id order by cast(dist as signed)";
$res333 = mysql_query($query333) or die('mult error');
while ($row333 = mysql_fetch_array($res333)) {
?>

<tr>
<td><?=$row333['ras']?></td>
<td><?=$row333['dcs']?></td>
<td><?=$row333['dist']?></td>	
<td><?=$row333['pangle']?></td>	
<td><?=$row333['jmag']?> &plusmn; <?=$row333['jerr']?></td>
<td><?=$row333['kmag']?></td>	
<td><?=$row333['dkmag']?></td>	
<td><?=$row333['prob_star']?></td>	
<td><?=$row333['prob_gal']?></td>	
<td><?=$row333['prob_noise']?></td>	
<td><?=$row333['kic']?>&nbsp;</td>	
<td><?=$row333['kic_kep']?>&nbsp;</td>	
<td><?=$row333['kic_dkep']?>&nbsp;</td>	
<td><?=$row333['kic_dist']?>&nbsp;</td>	
<td><?=$row333['user']?><br><?=$row333['date']?></td>
</tr>

<?
}
echo "</table>";
?>


<br><br>



<!-- UBV Multiplicity -->


<?
// Get total number of objects 
$mq2 = "select count(id) from mult_ubv where koi='$koi'";
$mqr2 = mysql_query($mq2);
$mqw2 = mysql_fetch_array($mqr2);
$nummul2 = $mqw2[0];
?>


<table width=94% border=1>
<tr>
<td colspan=16 align=center>
UBV Catalog Nearby Stars (<?=$nummul2?>)
</td>
</tr>

<tr>
<th>RA</th>
<th>Dec</th>
<th>Distance<br>(arcsec)</th>
<th>PA<br>(deg EofN)</th>
<th>U mag</th>
<th>B mag</th>
<th>V mag</th>
<th>VKepmag</th>
<th>d_Kepmag</th>
<th>KIC</th>
<th>KIC_Kepmag</th>
<th>KIC_dKepmag</th>
<th>KIC_Dist</th>
<th>User</th>
</tr>

<?
// Get multiplicity
$query322 = "select ra, dc, ras, dcs, dist, pa, umag, usig, bmag, bsig, vmag, 
vsig, vkep, dkep, kic, kic_kep, kic_dkep, kic_dist, date, user from mult_ubv where 
koi='$koi' order by cast(dist as signed)";
$res322 = mysql_query($query322) or die('mult2 error');
while ($row322 = mysql_fetch_array($res322)) {
?>

<tr>
<td><?=$row322['ras']?></td>
<td><?=$row322['dcs']?></td>
<td><?=$row322['dist']?>&nbsp;</td>
<td><?=$row322['pa']?>&nbsp;</td>
<td><?=$row322['umag']?> &plusmn; <?=$row322['usig']?></td>
<td><?=$row322['bmag']?> &plusmn; <?=$row322['bsig']?></td>
<td><?=$row322['vmag']?> &plusmn; <?=$row322['vsig']?></td>
<td><?=$row322['vkep']?>&nbsp;</td>
<td><?=$row322['dkep']?>&nbsp;</td>
<td><?=$row322['kic']?>&nbsp;</td>
<td><?=$row322['kic_kep']?>&nbsp;</td>
<td><?=$row322['kic_dkep']?>&nbsp;</td>
<td><?=$row322['kic_dist']?>&nbsp;</td>
<td><?=$row322['user']?><br><?=$row322[date]?></td>
</tr>

<?
}
echo "</table>";
?>


<br><br>


<!-- Files -->

<?
// Get total number of files
$fiquery = "select count(id) from uploaded_files where koi=$koi";
$fires = mysql_query($fiquery);
$firow = mysql_fetch_array($fires);
$finum = $firow[0];
?>


<a name="files"></a>


<table width=94% border=1>
<tr>
<td colspan=6 align=center>
Files (<?=$finum?>)
<a href="#">Add new</a>
</td>
</tr>

<tr>
<th>Primary Type</th>
<th>Secondary Type</th>
<th>File Name</th>
<th>Description</th>
<th>Date</th>
<th>User</th>
</tr>


<?
foreach ($file_types as $ftype) {
$query3 = "select id, koi, type, type2, filename, description, date, user 
from uploaded_files where koi=$koi AND deleted='N' AND type='$ftype' 
order by type, date DESC";
$res3 = mysql_query($query3) or die(mysql_error());

while ($row3 = mysql_fetch_array($res3)) {

echo "<tr>";
$fid = $row3["id"];
$fkoi = $row3["koi"];
$dir = $dirbase."targ{$fkoi}/".$row3['type']."/"; 
$url = $urlbase."targ{$fkoi}/".$row3['type']."/".$row3['filename'];
$fname = $dir.$row3['filename'];

echo "<td>$ftype</td>";
echo "<td>";
if ($row3['type2']!='') echo $row3['type2'];
echo "&nbsp;</td>";
echo "<td><a target=\"file\" href=\"$url\">$fname</a></td>";
?>

<td><?=$row3['description']?>&nbsp;</td>
<td><?=$row3['date']?></td>
<td><?=$row3['user']?></td>
</tr>

<?
} 
} 
?>

</table>

<br><br><br><br>

</center>
</body>
</html>



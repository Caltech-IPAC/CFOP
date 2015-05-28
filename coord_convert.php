<?
function coords_dec2sex($ra_dec, $dc_dec) {
$ra_dec = $ra_dec / 15;
$rah = intval($ra_dec);	
$rem1 = $ra_dec - $rah; 
$ram_temp = $rem1*60;		
$ram = intval($ram_temp);	
$rem2 = $ram_temp - $ram;	
$ras_temp = $rem2*60;		
$ras = round($ras_temp,2);	

if ($rah<10) $rah = "0$rah";
if ($ram<10) $ram = "0$ram";
if ($ras<10) $ras = "0$ras";
$ra = $rah . ":" . $ram . ":" . $ras;

$dc_dec_abs = abs($dc_dec);		
$decd = intval($dc_dec_abs);	
$rem3 = $dc_dec_abs - $decd;   	
$decm_temp = $rem3*60;			
$decm = intval($decm_temp);		
$rem4 = $decm_temp - $decm;		
$decs_temp = $rem4*60;			
$decs = round($decs_temp,2);	

if ($decd<10) $decd = "0$decd";
if ($decm<10) $decm = "0$decm";
if ($decs<10) $decs = "0$decs";
if ($dc_dec<0) $decd = "-$decd"; 
$dec = $decd . ":" . $decm . ":" . $decs;

return array($ra,$dec);
}

function coords_sex2dec($rah, $ram, $ras, $decd, $decm, $decs) {
$ra = round(($rah + ($ram/60) + ($ras/3600)),6);
$ra = $ra * 15;
$dec = round((abs($decd) + ($decm/60) + ($decs/3600)),6);
if ($decd < 0) $dec = 0 - $dec;

return array($ra, $dec);
}

function coords_dec2gal($ra_decimal, $dec_decimal) {
$ra_rad = deg2rad($ra_decimal);
$dec_rad = deg2rad($dec_decimal);
$rad1 = deg2rad(192.85946);
$rad2 = deg2rad(27.12825);
$rad3 = deg2rad(122.932);

$gal_lat_rad = asin(sin($rad2)*sin($dec_rad) + cos($rad2)*cos($dec_rad)*cos($ra_rad - $rad1));
$gal_long_rad = $rad3 - asin((cos($dec_rad)*sin($ra_rad - $rad1)) / (cos($gal_lat_rad)));
$gal_lat_deg = round(rad2deg($gal_lat_rad),6);
$gal_long_deg = round(rad2deg($gal_long_rad),6);

return array($gal_lat_deg, $gal_long_deg);
}
?>
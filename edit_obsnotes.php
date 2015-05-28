<?
// edit_obsnotes.php
// Display existing observing notes and provide a form for entering
// new notes for an individual KOI

include 'config.php';
$koi = $_GET['id'];

// Get target id from KOI
$query0 = "select id from kois where koi='$koi'";
$res0 = mysql_query($query0) or die(mysql_error());
while ($row = mysql_fetch_array($res0)) {
$tid = $row['id'];
}
?>

<html>
<body>
<center>

<form method="post" action="update_obsnotes.php">

<table width=65% border=0>
<tr>
<td>
<h4>Observing Notes for <?=$koi?></h4>

<textarea rows=10 cols=75 name="obsnotes"></textarea>
<br><br>
<input type="hidden" name="koi" value="<?=$koi?>">
<input type="hidden" name="tid" value="<?=$tid?>">
<input type="submit" value="Enter Notes">
</td>
</tr>
</table>

</form>
<br><br>

<table width=65% border=0>

<?
// Get existing notes and display them
$query = "select id, obsnotes, last_mod, last_mod_by from obs_notes 
where tid = $tid order by last_mod DESC";
$res = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_array($res)) {
$nid = $row["id"];
$lm = $row["last_mod"];
$lmb = $row["last_mod_by"];
$notes = $row['obsnotes'];
$notes = nl2br($notes);

echo "<tr>
<td><hr></td>
</tr>
<tr>
<td><font color=#888888><em>$lmb<br>$lm</em></font></td>
</tr>
<tr>
<td>$notes</td>
</tr>";
}
?>


</table>


<br><br>

</center>
</body>
</html>


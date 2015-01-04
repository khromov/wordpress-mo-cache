<?php
$ops = 1000000;
$mofile_string = '/var/www/html/wpdev/wp-content/plugins/email-obfuscate-shortcode/languages/email-obfuscate-shortcode-sv_SE.mo';

$mofile_string = $mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string.$mofile_string;
$mofile_string = $mofile_string.$mofile_string; //32x

echo 'Hashed string is: ' . $mofile_string;
echo '<br/><br/>';

/** Function to test **/
function preg_test($mofile, $wp_version, $self_version = '1.2.3')
{
	return preg_replace('/[^-\w]/', '_', $self_version . "-{$wp_version}-{$mofile}");
}

function md5_test($mofile, $wp_version, $self_version = '1.2.3')
{
	return md5($self_version . "-{$wp_version}-{$mofile}");
}

/** ---------------- TEST -----------------------------**/
echo "<strong>preg_replace test</strong><br/>";
$start = microtime(TRUE);
for($i = 0; $i < $ops; $i++)
{
	preg_test($mofile_string . rand(0, 100), '3.5.2');
}
$stop = microtime(TRUE);

$ops_per_second = $ops/($stop - $start);

echo "{$ops} operations total. {$ops_per_second} ops/s \n<br/>"; 
echo "Elapsed time: " . ($stop - $start) . " seconds\n <br/>";
echo "-------------------------------------------------<br/>";
/** ---------------- END TEST -----------------------------**/

/** ---------------- TEST -----------------------------**/
echo "<strong>md5 test</strong><br/>";
$start = microtime(TRUE);
for($i = 0; $i < $ops; $i++)
{
	md5_test($mofile_string . rand(0, 100), '3.5.2');
}
$stop = microtime(TRUE);

$ops_per_second = $ops/($stop - $start);

echo "{$ops} operations total. {$ops_per_second} ops/s \n<br/>"; 
echo "Elapsed time: " . ($stop - $start) . " seconds\n <br/>";
echo "-------------------------------------------------<br/>";
/** ---------------- END TEST -----------------------------**/
<?php

/**
 * Файл отображения дополнительного поля в административной части
 *
 * @filesource show.php
 * @author North-E <pushkov@kolosstudio.ru>
 * @version 1.0
 * @since 03.06.2009
 */

if($sParam2 == 'rate')
{
	$rating_field = $params['field'];
	$rating_line = $params['value'];
	$rating_expl = explode("|", $rating_line);
	$current_rating = floatval(str_replace(",", ".", $rating_expl[0]));
	$current_count = intval($rating_expl[1]);

	echo $current_rating . " (голосов &ndash; " . $current_count . ")";
}
else
{
	$rating_field = $params['field'];
	$rating_line = $params['value'];
	$current_rating = intval($rating_line);
	echo "Голосов &ndash; " . $current_rating;
}
?>

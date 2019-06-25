<?php
echo '<script src="https://js.stripe.com/v3/"></script>';
//shortcode param type
print_r($atts);
$opmCustomize = new OptimizeCustomize();
echo $opmCustomize->getSettingsCode();

echo '<p>version:' . OptimizeCustomize::VERSION . '</p>';

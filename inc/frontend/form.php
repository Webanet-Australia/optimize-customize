<?php

//stripe elements api
if (OptimizeCustomize::getPaymentProvider() == 'stripe') {
  print '<script src="https://js.stripe.com/v3/"></script>';
}

//the signup form
print OptimizeCustomize::getSignupForm();

//js object shortcode atts for form inputs
print '<script>var optimizeCustomizeAtts = ' . json_encode($atts) . '</script>';

?>

<script>

    jQuery(document).ready(function($) {

      //loop shortcodes object
      $.each(optimizeCustomizeAtts, function (key, value) {

        //add shortcode atts as hidden input controls to aform
        $('form').append('<input type="hidden" name="' + key + '" value="' + value + '" />');

      });

  });
</script>

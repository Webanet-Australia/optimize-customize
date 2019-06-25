<?php
    defined('ABSPATH') or die;
?>
<form method="POST" action="options.php"><?php

    settings_fields('optimize-customize');

    if (!is_plugin_active('optimizeMember/optimizeMember.php') and current_user_can('activate_plugins')) {
        echo '<div class="update-nag"><p><optimizePress plugin is not active.</p>
            <p>You will need to install and activate OptimizePress as well as configure OptimizeMember to use this plugin.</p>
            </div>';
    }

    do_settings_sections('optimize-customize');

    submit_button();?>
</form>
<table class="form-table">
  <tbody>
    <tr class="form-field form-required">
      <th scope="row">
        <label for="shortcodes">EG Usage:</label>
      </th>
      <td>
        <b>[optimize_customize level="1" currency="USD" plan="Payment Provider's Plan Name"/]</b><br/>
          <i>Level is an OptimizePress Membership level, where Bronze is one, etc.</i><br/>
          <i>Plan name, as setup on your payment gateways dashboard.</i><br/>
      </td>
    </tr>
  </tbody>
</table>
<script>
  jQuery(document).ready(function($) {
    var cm = CodeMirror.fromTextArea(document.getElementById("optimize-customize-code-editor"), {
      lineNumbers: true,
      lineWrapping: true,
      styleActiveLine: true,
      matchBrackets: true,
      mode: "htmlmixed"
    });

    cm.setSize('100%', '260px');

  });
</script>

<?php

/**
 * Admin interface: Support tab
 * Intro form displayed at the start to allow to chose "support request" type
 */

?>
<fieldset>
  <legend>Choose request type</legend>

  <table class="form-table">
    <tr>
      <th valign="top"><label>Request type:</label></th>
      <td>
        <select id="support_request_type" name="request_type">
          <option value="">-- Choose Type --</option>
          <option value="new_feature">Suggest A New Feature</option>
          <option value="bug_report">Submit A Bug Report</option>
        </select>
      </td>
    </tr>
  </table>
</fieldset>

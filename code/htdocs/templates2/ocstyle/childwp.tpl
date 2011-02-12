<form action="childwp.php" method="post" name="fchildwp">
  <input type="hidden" name="cacheid" value="{$cacheid|escape}" />

  <div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/description/22x22-waypoint.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Child waypoint{/t}" title="{t}Child waypoint{/t}" />
    {$pagetitle|escape}
  </div>

  <table class="table">
    <tr>
      <td valign="top">{t}Waypoint type:{/t}</td>
      <td>
        <select name="wp_type">
          <option value=0>Please select type</option>
          {html_options values=$wpTypeIds output=$wpTypeNames selected=$wpType}
        </select>
      </td>
    </tr>

    {if isset($wpTypeError)}
    <tr>
      <td></td>
      <td>
        {$wpTypeError}
      </td>
    </tr>
    {/if}

    <tr>
      <td valign="top">{t}Coordinate:{/t}</td>
      <td>
        {include file='coordinate_input.tpl'}
      </td>
    </tr>

    <tr>
      <td valign="top">{t}Description:{/t}</td>
      <td>
        <textarea name="desc" rows="5" cols="60">{$wpDesc}</textarea>
      </td>
    </tr>

    <tr>
      <td class="spacer" colspan="2"></td>
    </tr>

    <tr>
      <td></td>
      <td>
        <button type="submit" name="back" value="back" style="width:120px">{t}Cancel{/t}</button>&nbsp;&nbsp;
        <button type="submit" name="submitform" value="submit" style="width:120px">{t}Submit{/t}</button>
      </td>
    </tr>
  </table>
</form>

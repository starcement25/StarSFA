 <html>
    <body>
    <form method="POST">
    <table width="40%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
            <?php if( $_SERVER['REQUEST_METHOD'] == 'POST' ) { ?>
        		<tr>
          			<td style="color:#F00">
          			<strong>Invalid password</strong>
                    </td>
                </tr>    
      <?php } ?>
      <tr>
          <td>
          <strong>Enter password for access:</strong>
          </td>
      </tr>
      <tr>
          <td><input type="password" name="password"></td>
       </tr>
       <tr>
          <td>   
      <button type="submit">Submit</button>
      </td>
       </tr>
      </table>

    </form>
    </body>
    </html>
    
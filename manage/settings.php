<div class="mainCont">
	<div class="head">
    	Welcome to plexcloud config
    </div>
<div class="innerCont">
	<h3>Config</h3>
    <a class="button" href="../conf/logout.php">Logout</a>
    <hr />
    <div>
        <a name="general"></a>
        <h4>General Settings</h4>
        <form method="post" action="../conf/changeconf.php" enctype="multipart/form-data">
        <table>
            <tr>
            	<td>
                <strong>Username:</strong>
                <input type="text" name="usr" value="<?php echo $at->getUsername(); ?>" />
                </td>
                <td>
                    <strong>Password:</strong>
                <input type="text" name="pwd" value="" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Sabnzbd</h4>
        <form method="post" action="../conf/changeconf.php" enctype="multipart/form-data">
        <input type="hidden" name="t" value="sabnzbd" />
        <table>
        	<tr>
            	<td>
                <strong>Enabled: </strong><input type="checkbox" <?php if($conf->sab['enabled']) echo "checked"; ?> value="true" />
                </td>
                 <td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $conf->sab['apikey']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Server:</strong>
                <input type="text" name="sabserver" value="<?php echo $conf->sab['server']; ?>" />
                </td>
                <td>
                <strong>Port:</strong>
                <input type="text" name="sabport" value="<?php echo $conf->sab['port']; ?>" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Email Notification</h4>
        <form method="post" action="../conf/changeconf.php" enctype="multipart/form-data">
        <input type="hidden" name="t" value="emailnotification" />
        <table>
        	<tr>
            	<td>
                <strong>Enabled: </strong>
                <input type="checkbox" <?php if($conf->email['enabled']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Recieving address:</strong>
                <input type="text" name="sabserver" value="<?php echo $conf->email['address']; ?>" />
                </td>
                <td>
                <strong>Use smtp:</strong>
                <input type="checkbox" <?php if($conf->email['smtp']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>SMTP server:</strong>
                <input type="text" name="sabserver" value="<?php echo $conf->email['smtpserver']; ?>" />
                </td>
                <td>
                <strong>SMTP port:</strong>
                <input type="checkbox" <?php if($conf->email['smtpport']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Indexers</h4>
        <?php 
		$i=0;
		if($indexers === true){
			for($i; $i<count($indexsites); $i++){ 
				$inx = $indexsites[$i] ?>
                
                <form method="post" action="../conf/changeconf.php" enctype="multipart/form-data">
                <input type="hidden" name="t" value="indexsite" />
                <input type="hidden" name="index" value="<?php echo $i; ?>" />
                <input type="text" disabled="disabled" value="<?php echo $i; ?>" />
                <table>
                    <tr>
                        <td>
                        <strong>Name:</strong>
                        <input type="text" name="name" value="<?php echo $inx->getName(); ?>" />
                        </td>
                        <td>
                        <strong>Enabled: </strong><input type="checkbox" <?php if($inx->enabled) echo "checked"; ?> value="true" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <strong>Url:</strong>
                        <input type="text" name="url" value="<?php echo $inx->getUrl(); ?>" />
                        </td>
                        <td>
                        <strong>ApiKey:</strong>
                        <input type="text" name="apikey" value="<?php echo $inx->getApiKey(); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <input type="submit" value="save" />
                       
                        </td>
                   </tr>
                </table>
                </form>
                <br />
				<?php	
			}
		} ?>
        <form method="post" action="../conf/changeconf.php" enctype="multipart/form-data">
        <input type="hidden" name="t" value="indexsite" />
        <input type="hidden" name="index" value="<?php echo $i; ?>" />
        <input type="text" disabled="disabled" value="<?php echo $i; ?>" />
        <table>
            <tr>
                <td>
                <strong>Name:</strong>
                <input type="text" name="name" value="" />
                </td>
                <td>
                <strong>Enabled: </strong><input type="checkbox" value="true" />
                </td>
            </tr>
            <tr>
                <td>
                <strong>Url:</strong>
                <input type="text" name="url" value="" />
                </td>
                <td>
                <strong>ApiKey:</strong>
                <input type="text" name="sabport" value="" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
    </div>
</div>
</div>
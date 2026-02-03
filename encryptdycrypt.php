<?php

              class MCrypt
              {
                private $iv = 'fedcba9876543210'; #Same as in JAVA
                private $key = '0123456789abcdef'; #Same as in JAVA


                function __construct()
                {
                }

                function encrypt($str) {

                  //$key = $this->hex2bin($key);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $encrypted = mcrypt_generic($td, $str);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return bin2hex($encrypted);
                }

                function decrypt($code) {
                  //$key = $this->hex2bin($key);
                  $code = $this->hex2bin($code);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $decrypted = mdecrypt_generic($td, $code);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return utf8_encode(trim($decrypted));
                }

                protected function hex2bin($hexdata) {
                  $bindata = '';

                  for ($i = 0; $i < strlen($hexdata); $i += 2) {
                        $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
                  }

                  return $bindata;
                }

            }
            if($_REQUEST['sub']){
              $mcrypt = new MCrypt();
              if($_REQUEST['etype']==1){
                $content=$_REQUEST['encdncval'];
                $result = $mcrypt->encrypt($content);
              }
              if($_REQUEST['etype']==2){
                $content=$_REQUEST['encdncval'];
                $result = $mcrypt->decrypt($content);
              }
           }



?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
/*
/* Created by Filipe Pina
 * Specific styles of signin, register, component
 */
/*
 * General styles
 */
#playground-container {
    height: 500px;
    overflow: hidden !important;
    -webkit-overflow-scrolling: touch;
}
body, html{
  height: 100%;
 	background-repeat: no-repeat;
  background-size: cover;
}

.main{
 	margin:50px 15px;
}

h1.title {
	font-size: 50px;
	font-family: 'Passion One', cursive;
	font-weight: 400;
}

hr{
	width: 10%;
	color: #fff;
}

.form-group{
	margin-bottom: 15px;
}

label{
	margin-bottom: 15px;
}

input,
input::-webkit-input-placeholder {
    font-size: 11px;
    padding-top: 3px;
}

.main-login{
 	background-color: #fff;
    /* shadows and rounded borders */
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);

}
.form-control {
    height: auto!important;
padding: 8px 12px !important;
}
.input-group {
    -webkit-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.21)!important;
    -moz-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.21)!important;
    box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.21)!important;
}
#button {
    border: 1px solid #ccc;
    margin-top: 28px;
    padding: 6px 12px;
    color: #666;
    text-shadow: 0 1px #fff;
    cursor: pointer;
    -moz-border-radius: 3px 3px;
    -webkit-border-radius: 3px 3px;
    border-radius: 3px 3px;
    -moz-box-shadow: 0 1px #fff inset, 0 1px #ddd;
    -webkit-box-shadow: 0 1px #fff inset, 0 1px #ddd;
    box-shadow: 0 1px #fff inset, 0 1px #ddd;
    background: #f5f5f5;
    background: -moz-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f5f5f5), color-stop(100%, #eeeeee));
    background: -webkit-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
    background: -o-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
    background: -ms-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
    background: linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f5f5f5', endColorstr='#eeeeee', GradientType=0);
}
.main-center{
 	margin-top: 30px;
 	margin: 0 auto;
  padding: 10px 40px;
  text-shadow: none;
	-webkit-box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.31);
  -moz-box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.31);
  box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.31);

}
span.input-group-addon i {
    color: #009edf;
    font-size: 17px;
}

.login-button{
	margin-top: 5px;
}

.login-register{
	font-size: 11px;
	text-align: center;
}
</style>
<div class="container">
			<div class="row main">
				<div class="main-login main-center">
          <form action="" method="post" id="contactFrm" name="form-inline">
            <div class="form-group">
             <div class="cols-sm-10">
               <div class="input-group">
                    <textarea name="encdncval" class="form-control" style="margin: 0px; width: 329px; height: 163px;"><?php echo (($_REQUEST['encdncval'])?$_REQUEST['encdncval']:'') ?></textarea>
                </div>
            </div>
            </div>
            <div class="form-group">
             <div class="cols-sm-10">
               <div class="input-group">
                 <input type="radio" name="etype" value="1" <?php if($_REQUEST['etype']==1) { ?>checked="checked" <?php } ?>>Encrypt
                 <input type="radio" name="etype" value="2" <?php if($_REQUEST['etype']==2) { ?>checked="checked" <?php } ?>>Decrypt
                </div>
            </div>
            </div>
            <div class="form-group">
             <div class="cols-sm-10">
               <div class="input-group">
                 <input type="submit" name="sub" value="Encrypt/Decrypt">
                </div>
            </div>
            </div>
          </form>
          <div>
            <?php if($_REQUEST['etype']=='1' && $result) { ?>
              Encrypt Value:   <textarea  class="form-control" style="margin: 0px; width: 329px; height: 163px;"><?php echo $result ?></textarea>
            <?php } ?>
            <?php if($_REQUEST['etype']=='2' && $result) { ?>
              Decrypt Value: <textarea  class="form-control" style="margin: 0px; width: 329px; height: 163px;"><?php echo $result ?></textarea>
            <?php } ?>
          </div>
        </div>
			</div>
</div>

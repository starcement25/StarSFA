<?php

	//Set the Content Type
     /* header('Content-type: image/jpeg');

      // Create Image From Existing File
      $jpg_image = imagecreatefromjpeg('upload/STAR/E027220160418122709.jpeg');

      // Allocate A Color For The Text
      $white = imagecolorallocate($jpg_image, 0, 0, 182);

      // Set Path to Font File
      $font_path = 'Xanadu.ttf';
	  
	  $fontSize = 3;
		$x = 115;
		$y = 185;

      // Set Text to Be Printed On Image
      $text = "This is a sunset!";

      // Print Text On Image
      imagettftext($jpg_image, $fontSize, $x, $y,300, $white, $font_path, $text);

      // Send Image to Browser
      imagejpeg($jpg_image);

      // Clear Memory
      imagedestroy($jpg_image);*/
	  list($width, $height)=getimagesize($_SERVER['DOCUMENT_ROOT']."/acednsproduct/upload/STAR/E035520160420161118.jpeg");
	  $text1='Reliance Cement';
	  $text2='Dhakuria';
	  
	  
?>
<canvas id="e" height="<?php echo $height; ?>" width="<?php echo $width; ?>"></canvas>
<script>
  var canvas = document.getElementById("e");
  var context = canvas.getContext("2d");
 	var imageObj = new Image();
     imageObj.onload = function(){
         context.drawImage(imageObj, 5, 5);
         context.font = "20pt Calibri";
		 context.fillStyle="#FFFF00";
		 context.strokeStyle="#880000";
         context.fillText("<?php echo $text1;?>", 20, 50);
		 context.fillText("<?php echo $text2;?>", 20, 80);
     };
     imageObj.src = "upload/STAR/E035520160420161118.jpeg"; 
</script>
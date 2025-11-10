<?php
   $num1=$_GET['num1'];
   $num2=$_GET['num2'];
   $op=$_GET['operacion'];
   $resultado=0;

   switch($op){
        case "suma":
            $resultado=$num1+$num2;
        break;
        case "resta":
            $resultado=$num1-$num2;
        break;
        case "mult":
            $resultado=$num1*$num2;
        break;
        case "div":
            $resultado=$num1/$num2;
        break;
        default:
            echo"Elige uno";
            exit;
        break;
   }

   echo "*********************************************<br>";
   echo" ********El resultado es $resultado **************<br>";
   echo "*********************************************<br>";
?>
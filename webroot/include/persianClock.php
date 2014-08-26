<? if($_POST['ajax'] == 'run') {
   include 'Smarty/plugins/jdf.class.php';
   print jdate('H:i:s').' - '.jdate('Y/n/j');
   }
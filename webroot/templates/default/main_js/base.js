// Js By seyed amirhossein tavo@si 
// Y! :: tamirtavoosi
window.setInterval(function persianclock() {
    $.post(MELODYURL+'/include/persianClock.php','ajax=run',function(data){
            $('date').html(data);
    });
}, 10);
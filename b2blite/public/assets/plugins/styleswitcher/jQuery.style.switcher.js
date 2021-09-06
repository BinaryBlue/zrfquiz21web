// Theme color settings
$(document).ready(function(){
 
    var currentThemes = $.cookie("sximo6-theme") ;
    if( typeof (currentThemes) !== 'undefined')
    {
        $('#theme').attr({href: currentThemes })
    }
    // color selector
    $('#themecolors').on('click', 'a', function(){

        var url = $(this).attr('data-url')
      //  $('#themecolors li a').removeClass('working');
         $('#theme').attr({href: url})
         $.cookie("sximo6-theme",url, {expires: 365, path: '/'});

       // $(this).addClass('working')
      });

});

/*
$(document).ready(function(){
    $("*[data-theme]").click(function(e){
      e.preventDefault();
        var currentStyle = $(this).attr('data-theme');
        store('theme', currentStyle);
        $('#theme').attr({href: 'css/colors/'+currentStyle+'.css'})
    });

    var currentTheme = get('theme');
    if(currentTheme)
    {
      $('#theme').attr({href: 'css/colors/'+currentTheme+'.css'});
    }
    // color selector
$('#themecolors').on('click', 'a', function(){
        $('#themecolors li a').removeClass('working');
        $(this).addClass('working')
      });
});*/

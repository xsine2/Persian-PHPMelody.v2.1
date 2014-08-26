/* ------------------------------------------------------------- */
// $PLUGIN DEFAULT OPTIONS
/* ------------------------------------------------------------- */

$('video').mediaelementplayer ({

    // auto-select this language (instead of starting with "None")
    startLanguage:'en',
    // automatically translate into these languages
    translations:['es','ar','zh','ru'],
    // enable the dropdown list of languages
    translationSelector: true,

    // path to Flash and Silverlight plugins
    pluginPath: 'addons/',

    alwaysShowControls: true,

    videoVolume: 'horizontal',
    // features to show
    features: ['playpause', 'stop', 'loop','current','progress','duration','tracks','volume', 'sourcechooser', 'playlist', 'fullscreen', 'postroll'],

});//end mediaelementplayer


$('audio').mediaelementplayer ({

    defaultVideoWidth: -1,
    
    videoVolume: 'horizontal',
    // features to show
    features: ['playpause', 'stop', 'loop','current','progress','duration', 'volume'],

});//end mediaelementplayer


/* ------------------------------------------------------------- */
// 
/* ------------------------------------------------------------- */
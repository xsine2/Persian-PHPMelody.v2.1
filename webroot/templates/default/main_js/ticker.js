// Control parameters
var theCharacterTimeout = 90;
var theStoryTimeout = 10000;
var theWidgetOne = "_";
var theWidgetTwo = "-";
var theWidgetNone = "";
var theItemCount = theSummaries.length;
var NS6 = (document.getElementById && !document.all) ? true : false;

// Ticker startup

function startTicker() {

    // Define run time values
    theCurrentStory = -1;
    theCurrentLength = 0;
    // Locate base objects
    if (document.getElementById) {
        runTheTicker();
    }
    else {
        document.write("<style>.ticki{display:none;}.ticko{border:0px; padding:0px;}</style>");
        return true;
    }
}
// Ticker main run loop
function runTheTicker() {
    var myTimeout;
    // Go for the next story data block
    if (theCurrentLength == 0) {
        theCurrentStory++;
        theCurrentStory = theCurrentStory % theItemCount;
        theStorySummary = theSummaries[theCurrentStory];
        theTargetLink = theSiteLinks[theCurrentStory];
    }

    var textTitle = theStorySummary.substring(0, theCurrentLength) + whatWidget();

    if (theTargetLink) {

        if (NS6) {
            document.getElementById("theTicker").innerHTML = '<a  target="_self" style="font-size:13px; color:#fff" href="' + theTargetLink + '" target="_top">' + textTitle + '</a>';
        }
        else {
            document.all.theTicker.innerHTML = '<a  target="_self" style="font-size:13px; color:#fff" href="' + theTargetLink + '" target="_top">' + textTitle + '</a>';
        }
    }
    else {
        if (NS6) {
            document.getElementById("theTicker").innerHTML = '<span>' + textTitle + '</span>';
        }
        else {
            document.all.theTicker.innerHTML = '<span>' + textTitle + '</span>';
        }
    }

    // Modify the length for the substring and define the timer
    if (theCurrentLength != theStorySummary.length) {
        theCurrentLength++;
        myTimeout = theCharacterTimeout;
    }
    else {
        theCurrentLength = 0;
        myTimeout = theStoryTimeout;
    }
    // Call up the next cycle of the ticker
    setTimeout("runTheTicker()", myTimeout);
}

// Widget generator
function whatWidget() {
    if (theCurrentLength == theStorySummary.length) {
        return theWidgetNone;
    }

    if ((theCurrentLength % 2) == 1) {
        return theWidgetOne;
    }
    else {
        return theWidgetTwo;
    }
}

startTicker();
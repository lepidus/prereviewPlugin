/**
 * @file plugins/generic/prereviewPlugin/js/prereview.js

 * @package plugins.generic.prereviewPlugin
 *
 */
//Functions for showing and hiding FullReviews
function more(id) {
    var div = "div-" + id;
    var btn = "btn-" + id;
    var less = "less-" + id;
    document.getElementById(div).style.display = "block";
    document.getElementById(btn).style.display = "none";
    document.getElementById(less).style.display = "block";
}

function less(id) {
    var div = "div-" + id;
    var btn = "btn-" + id;
    var less = "less-" + id;
    document.getElementById(div).style.display = "none";
    document.getElementById(btn).style.display = "block";
    document.getElementById(less).style.display = "none";
}
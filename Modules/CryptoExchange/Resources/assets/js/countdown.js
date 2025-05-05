"use strict";

var deadline = new Date(expireTime).getTime();
let x = setInterval(function() {
var now = new Date().getTime();
var t = deadline - now;
var days = Math.floor(t / (1000 * 60 * 60 * 24));
var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
var seconds = Math.floor((t % (1000 * 60)) / 1000);
document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
    if (t < 0) {
        clearInterval(x);
        document.getElementById("timer").innerHTML = expireText;
        $(".submit-button").attr("disabled", true);
        $("#pay_with").attr("disabled", true);
        $("#receive_with").attr("disabled", true);

    }
}, 1000);

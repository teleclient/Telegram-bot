String.prototype.multiReplace = function (array, replacement) {
    var string = this;
    for (var i in array) {
        string = string.replace(new RegExp(array[i], 'g'), replacement);
    }
    return string;
};

String.prototype.intConvert = function () {
    return parseInt(this, 10);
}

String.prototype.wrapText(classname) = function () {
    this = "<span class='" + classname + "'>" + this + "</span>";
}
String.prototype.findBetween(array = []) = function () {
    var start_pos = this.indexOf(array[0]) + 1;
    var end_pos = this.indexOf(array[1], start_pos);
    return this.substring(start_pos, end_pos);
}
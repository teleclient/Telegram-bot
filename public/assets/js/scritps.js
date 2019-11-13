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

String.prototype.wrapText = function (classname) {
    var string = this;
    return "<span class='" + classname + "'>" + string + "</span>";
}

String.prototype.findBetween = function (array = []) {
    var start_pos = this.indexOf(array[0]) + 1;
    var end_pos = this.indexOf(array[1], start_pos);
    return this.substring(start_pos, end_pos);
}

String.prototype.wrapBetween = function (array = [], classname) {
    var $this = this;
    var $true = true;

    while ($true) {
        string = $this.findBetween(["%", "%"]);
        if (string == null || string == "" || string == " ") {
            $true = false;
            return $this;
        }
        var replacement = string.wrapText(classname);
        var $this = $this.multiReplace(['%' + string + '%'], replacement);
    }
}
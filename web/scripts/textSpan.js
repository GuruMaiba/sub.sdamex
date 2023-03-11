function addSpan(text) {
    // SCRIPT GET WORD IN THE PARAGRATH
    var words = text.split(/\s+/);
    var text = words.join("</span> <span>");
    return "<span>" + text + "</span>";
}

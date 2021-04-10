function getRandomMapLocation() {
    var x = getRandom(store.map.width-1);
    var y = getRandom(store.map.height-1);
    return { x: x, y: y };
}
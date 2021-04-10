function checkCombat() {
    if (store.player.atk <=1) {
        playerMessage("You can't attack.  You're too weak.  Find your strength!");
    }
}

function isMonster(loc) {
    return store.enemies.findIndex((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y)) > -1;
}

function getMonsterAt(loc) {
    return store.enemies.find((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y));
}

function isItem(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    return index > -1;
}

function selectItem(id) {
    return store.items.find((item) => item.id == id);
}

function getItemAt(loc) {
    var item = store.mapItems.find((item) => (item.x == loc.x) && (item.y == loc.y));
    return selectItem(item.itemID);
}

function isEdge(location) {
    if ((location.x < 0) || (location.y < 0) || (location.x > store.map.width-1) || (location.y > store.map.height-1)) {
        return true;
    }
    return false;
}


function movePlayerDelta(x, y) {
    clearMessage();
    var newLoc = { x: store.player.x + x, y: store.player.y + y };
    if (isEdge(newLoc)) {
        return;
    }
    if (isMonster(newLoc)) {
        checkCombat()
        return;
    }

    if (isItem(newLoc)) {
        checkItem(newLoc);
        return;
    }

    store.player.x = newLoc.x;
    store.player.y = newLoc.y;
    render();

}

function checkItem(loc) {
    var item = getItemAt(loc);
   
    playerMessage("You found an "+ item.name);
    removeItemFromMap(loc);
    addPlayerItem(item);
    render();
    
}

function addPlayerItem(item) {
    store.playerItems.push(item);

    console.log(store);
}

function removeItemFromMap(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    store.mapItems.splice(index, 1);
}

function movePlayer(x, y) {
    store.player.x = x;
    store.player.y = y;
    render();
}




function isAvailable(location) {
    // player
    if (location.x == store.player.x && location.y == store.player.y) {
        return false;
    }
    // enemy 
    if (isMonster(location)){
        return false;
    }
    // item
    if (isItem(location)) {
        return false;
    }
    return true;
}

function spawnEnemy(x, y) {
    var enemy = { x: x, y: y, hp: 5 };
    store.enemies.push(enemy);
}

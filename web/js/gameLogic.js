
function isMonster(loc) {
    return store.activeEnemies.findIndex((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y)) > -1;
}

function getMonsterAt(loc) {
    var activeEnemy = store.activeEnemies.find((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y));
    var enemyType = store.enemies.find((enemy) => enemy.type === activeEnemy.type);
    return { activeEnemy, enemyType };
}

function isItem(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    return index > -1;
}

// probably could be named find or get, but used to doing Angular NGRX
function selectItem(id) {
    return store.items.find((item) => item.id == id);
}

function getItemAt(loc) {
    var item = store.mapItems.find((item) => (item.x == loc.x) && (item.y == loc.y));
    return selectItem(item.itemID);
}

// boundaries of map
// in the future the map could be populated with immovable wall items
function isEdge(location) {
    if ((location.x < 0) || (location.y < 0) || (location.x > store.map.width - 1) || (location.y > store.map.height - 1)) {
        return true;
    }
    return false;
}

function getRandomMapLocation() {
    var x = getRandom(store.map.width - 1);
    var y = getRandom(store.map.height - 1);
    return { x: x, y: y };
}

function movePlayerDelta(x, y) {
    clearMessage();
    var newLoc = { x: store.playerLoc.x + x, y: store.playerLoc.y + y };
    if (isEdge(newLoc)) {
        return;
    }
    if (isMonster(newLoc)) {
        checkCombat(newLoc)
        return;
    }

    if (isItem(newLoc)) {
        checkItem(newLoc);
        return;
    }

    store.playerLoc.x = newLoc.x;
    store.playerLoc.y = newLoc.y;
    render();

}

function movePlayer(x, y) {
    store.playerLoc.x = x;
    store.playerLoc.y = y;
    render();}


function persistGameData(data) {
    data.playerID = store.player.id;
    store.pause = true;
    return $.post("api/gameData.php", JSON.stringify(data), function (result) {
        var dbData = JSON.parse(result);
        store = { ...store, ...dbData };
        store.pause = false;
    });
}

function persistPlayer(player) {
    store.pause = true;
    return $.post("api/playerPOST.php", JSON.stringify(player), function (result) {
        var dbPlayer = JSON.parse(result);
  
        store.player = dbPlayer;
        store.pause = false;
    });
}

function isAvailable(location) {
    // player
    if (location.x == store.playerLoc.x && location.y == store.playerLoc.y) {
        return false;
    }
    // enemy 
    if (isMonster(location)) {
        return false;
    }
    // item
    if (isItem(location)) {
        return false;
    }
    return true;
}

function spawnEnemy(x, y) {
    var enemy = { x: x, y: y, hp: 5, type: 'demon' };
    store.activeEnemies.push(enemy);
}

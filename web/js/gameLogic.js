function checkCombat(loc) {
    if (store.player.atk <= 1) {
        playerMessage("You can't attack.  You're too weak.  Find your strength!");
        return;
    }
    var monster = getMonsterAt(loc);
    var damage = getAttackResult(store.player.atk);
    damage -= monster.enemyType.def;

    if (damage >= monster.activeEnemy.hp) {
        var promise = killMonster(monster.enemyType, store.player.id);
        $.when(promise).done(() => {
            // make it real on the server first
           
            removeMonsterAt(loc);
            render();
        });
    } else {
        monster.activeEnemy.hp -= damage;
        
        var monsterDamage = getAttackResult(monster.enemyType.atk);
        monsterDamage -= store.player.def;
        var promise = changePlayerHP(-monsterDamage, store.player.id);
        $.when(promise).done(() => {
            // make it real on the server first
            playerMessage("You attack! Damage = " + damage + " But enemy hits for: " + monsterDamage);
            render();
            checkDead();
        });
       

    }

}



function checkDead() {
    if (store.player.hp < 0) {
        alert("You have died!  Resetting game....");
        reset();
    }
}

function removeMonsterAt(loc) {
    var index = store.activeEnemies.findIndex((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y));
    store.activeEnemies.splice(index, 1);
}

function getAttackResult(atk) {
    var damage = 0;

    // you get one swing per attack value
    for (var i = 0; i < atk; i++) {
        damage += getRandom(6);
    }

    return damage;
}

function killMonster(enemyType, playerID) {
    store.pause = true;
   // will update player and player items
    return $.post("api/rewardPlayer.php", {enemyID : enemyType.id, playerID: store.player.id}, function (result) {
        var dbResult = JSON.parse(result);
        console.log('dbplayer', dbResult);
        var rewardedItem = selectItem(dbResult.rewardedItemID);
    
        playerMessage("You attack! And kill the " + enemyType.name + ". It gives you a " + rewardedItem.name + " and " + dbResult.rewardedXP + " XP!");
        store.player = dbResult.player;
        store.playerItems = dbResult.playerItems? dbResult.playerItems : [];
        store.pause = false;
        render();
    });
}

function changePlayerHP(hp, playerID) {
    var clonePlayer = { ...store.player };
    clonePlayer.hp += hp;
    return persistPlayer(clonePlayer, store.player.id);
}




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

function selectItem(id) {
    return store.items.find((item) => item.id == id);
}

function getItemAt(loc) {
    var item = store.mapItems.find((item) => (item.x == loc.x) && (item.y == loc.y));
    return selectItem(item.itemID);
}

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
    render();
}


function persistGameData(data) {

    if (data.playerItems && data.playerItems.length == 0) {
        data.playerItems = "empty";
    }
    data.playerID = store.player.id;
    store.pause = true;
    return $.post("api/gameData.php", data, function (result) {
        var dbData = JSON.parse(result);
        store = { ...store, ...dbData };
        if (store.playerItems === 'empty') {
            store.playerItems = [];
        }
        store.pause = false;
    });
}

function persistPlayer(player) {
    store.pause = true;
    return $.post("api/playerPOST.php", player, function (result) {
        var dbPlayer = JSON.parse(result);
        console.log('dbplayer', dbPlayer);
        store.player = dbPlayer;
        store.pause = false;
    });
}

function isAvailable(location) {
    // player
    if (location.x == store.playerLoc.x && location.y == store.player.y) {
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
